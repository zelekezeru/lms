<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Instructor;
use App\Models\Weight;
use App\Models\Semester;
use App\Models\Result;
use App\Models\Year;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\SemesterStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ResultsImport implements ToCollection
{
    // These properties should be more specific to what they hold
    protected $courseId;
    protected $sectionId;

    // Use these for caching data to prevent N+1 queries
    protected $students;
    protected $courseOfferings;
    protected $years;
    protected $semesters;
    protected $weightsData = [];
    protected $noGrade = [];
    protected $course;
    protected $results = [];

    public function __construct($courseId, $sectionId)
    {
        $this->courseId = $courseId;
        $this->sectionId = $sectionId;
        $this->course = Course::find($courseId);
    }

    /**
     * @param Collection $rows
     * @throws Exception
     */
    public function collection(Collection $rows)
    {
        // Check for empty file
        if ($rows->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'The uploaded file is empty. Please upload a valid file with data.']);
        }

        $this->course = Course::find($this->courseId);

            // Extract and validate weight points from the first row
            $headerRow = $rows->first()->toArray();
            $weightPoints = array_slice($headerRow, 4);

            if (empty($weightPoints)) {
                return redirect()->back()->withErrors(['error' => 'No weight points found in the file. Please ensure the header row contains weight percentages from the 5th column onwards.']);
            }

            // Validate if the total weight points sum to 100
            if (array_sum($weightPoints) != 100) {
                return redirect()->back()->withErrors(['error' => 'Total weight points must sum to 100%. Please check the file and try again.']);
            }
            
            // Get the student ID numbers from the data rows for pre-loading
            $studentIdNumbers = $rows->slice(1)->pluck(1)->filter()->unique()->toArray();


                // Pre-load all necessary data to avoid N+1 queries
            $this->students = Student::whereIn('id_no', $studentIdNumbers)->with('section')->get()->keyBy('id_no');
            
            // Process student rows, starting from the second row (index 1)
            // $studentRows = $rows->slice(1);
            foreach ($rows->slice(1) as $row) {
                // Ensure $row is an array
                $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                
                // Validate the row length to ensure it has enough columns
                if (count($rowArray) < 5) {
                    Log::warning('Row skipped due to insufficient columns: ' . json_encode($rowArray));
                    continue; // Skip rows that do not have enough data
                }

                // Ensure the student ID number is present
                if (empty($rowArray[1])) {
                    Log::warning('Row skipped due to missing student ID: ' . json_encode($rowArray));
                    continue; // Skip rows without a student ID
                }

                $student = $this->students->get(trim($rowArray[1]));
                $section = Section::find($student->section_id ?? $this->sectionId);                
                $studyMode = $section->studyMode;

                $courseOfferings = CourseOffering::where('course_id', $this->courseId)
                    ->where('section_id', $section->id) // Use student's section if available, otherwise use provided section ID
                    ->with('course', 'section.studyMode.semesters', 'instructor')
                    ->get()->keyBy(function ($item) {
                        // Create a unique key for lookup if needed, or just get the first one
                        return $item->section_id;
                    });
                // Find the single course offering we are interested in
                $courseOffering = $courseOfferings->get($section->id);
                
                if (!$courseOffering) {
                    return redirect()->back()->withErrors(['error' => 'Course offering not found for the student\'s section.']);
                }

                // Validate year and semester levels are set for the course offering
                if (!$courseOffering->year_level || !$courseOffering->semester_level) {
                    return redirect()->back()->withErrors(['error' => 'Year level or semester level not set for the course offering.']);
                }

                // Determine the academic year based on the section's year and course offering's year level
                $courseGivenAtYearName = intval($section->year->name) + $courseOffering->year_level - 1;
                
                $year = Year::where('name', $courseGivenAtYearName)->first();
                
                if (!$year) {
                    return redirect()->back()->withErrors(['error' => 'Year not found for the course offering.']);
                }

                // Find the semester
                $semester = $this->findSemester($studyMode, $year, $courseOffering);
                
                // Get related models from the single course offering
                $instructor = $courseOffering->instructor ?? Instructor::first();
                
                // Register the student for the semester if not already registered
                if (SemesterStudent::where('student_id', $student->id)->where('semester_id', $semester->id)->doesntExist()) {
                    $semesterReg = $this->registerSemester($student, $semester);
                }
                
                // Create/update weights for this course and section once outside the student loop
                $weights = $this->createWeights($weightPoints, $instructor, $section, $semester, $this->course);

                // Student result create method
                $totalPoint = $this->createResults($weights, $student, $courseOffering, $rowArray);

                if ($totalPoint === 0) {
                    // If no valid scores were found, skip to the next student
                    continue;
                }
                
                // After processing results, calculate the total points for grading
                if (collect($this->noGrade)->contains('student_id', $student->id)) {
                    $this->noGrade[] = [
                        'student_id' => $student->id,
                    ];
                    $this->createGrade($totalPoint, $student, $this->course, $section, $year, $semester, $courseOffering, false);
                } else {
                    // Create or update the grade for the student
                    $this->createGrade($totalPoint, $student, $this->course, $section, $year, $semester, $courseOffering, true);
                }
            }

            // If everything is successful, commit the transaction
            DB::commit();
    }

    /**
     * Creates or updates the weight records for the course.
     * This method is called once per import.
     *
     * @param array $weightPoints
     * @param Instructor $instructor
     * @param Section $section
     * @param Semester $semester
     * @param Course $course
     * @return array
     */
    public function createWeights($weightPoints, $instructor, $section, $semester, $course): array
    {
        $weights = [];
        foreach ($weightPoints as $index => $weightPoint) {
            if (!is_numeric($weightPoint)) {
                continue; // Skip non-numeric weight points
            }

            // Use the find() method on a preloaded collection to find or create the weight
            $weight = Weight::updateOrCreate(
                [
                    'name' => 'Weight ' . ($index + 1),
                    'section_id' => $section->id,
                    'semester_id' => $semester->id,
                    'course_id' => $course->id,
                ],
                [
                    'instructor_id' => $instructor->id,
                    'point' => $weightPoint,
                    'description' => null,
                ]
            );
            $weights[$index] = $weight;
        }
        return $weights;
    }

    /**
     * Creates or updates the result records and calculates the weighted total.
     *
     * @param array $weights
     * @param Student $student
     * @param Course $course
     * @param CourseOffering $courseOffering
     * @param Collection $row
     * @return float
     */
    private function createResults($weights, $student, $courseOffering, $row): float
    {
        $totalWeightedPoint = 0;

        foreach ($weights as $index => $weight) {
            // The score is in the column corresponding to the weight index + 4
            $score = $row[$index + 4] ?? null;

            // --- IMPROVED ERROR HANDLING ---

            // If all scores of the student are empty don't add to noGrade
            if (collect($this->noGrade)->contains('student_id', $student->id)) {
                $this->noGrade = collect($this->noGrade)->reject(function ($item) use ($student) {
                    return $item['student_id'] === $student->id;
                })->values()->all();

                // If no scores are present, skip the student
                if (empty($row->slice(4)->filter()->all())) {
                    continue;
                }
            }

            // Check if the score is not numeric and log a warning instead of just skipping.
            if (!is_numeric($score) || is_null($score) || trim($score) === '') {
                // Only add if not already present
                if (!collect($this->noGrade)->contains('student_id', $student->id)) {
                    $this->noGrade[] = [
                        'student_id' => $student->id,
                    ];
                }
                
                continue;
            }

            $score = floatval($score);
            
            // Update or create the Result entry
            $this->results[$student->id][$weight->id] = Result::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'weight_id' => $weight->id,
                ],
                [
                    'point' => $score,
                    'instructor_id' => $courseOffering->instructor_id ?? Auth::id(),
                    'description' => 'Imported from Excel',
                ]
            );

            // Correct calculation: multiply score by weight percentage
            $totalWeightedPoint += $score;
        }

        return $totalWeightedPoint;
    }

    /**
     * Creates or updates the final grade and enrollment status.
     *
     * @param float $totalPoint
     * @param Student $student
     * @param Course $course
     * @param Section $section
     * @param Year $year
     * @param Semester $semester
     * @param CourseOffering $courseOffering
     */
    private function createGrade($totalPoint, $student, $course, $section, $year, $semester, $courseOffering, $isImport = true)
    {
        if ($isImport === false || $totalPoint === 0) {
            $gradeLetter = "NG"; 
            $academicStatus = 'in_progress';
        } else {
            $totalPoint = floatval($totalPoint);
            $gradeLetter = $this->getGradeLetter($totalPoint);
            $academicStatus = ($gradeLetter === 'F') ? 'failed' : 'completed';
        }

        // Update or create the Grade entry
        Grade::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'section_id' => $section->id,
            ],
            [
                'year_id' => $year->id,
                'semester_id' => $semester->id,
                'grade_point' => $totalPoint,
                'grade_letter' => $gradeLetter,
                'user_id' => Auth::id(),
                'grade_status' => 'Submitted',
                'grade_scale' => 100,
                'grade_description' => 'Excel Imported',
            ]
        );

        // Update or create the enrollment status
        Enrollment::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_offering_id' => $courseOffering->id,
                'semester_id' => $semester->id,
            ],
            [
                'status' => 'enrolled',
                'academic_status' => $academicStatus,
            ]
        );
    }

    /**
     * Returns the grade letter based on the total point.
     *
     * @param float|int $totalPoint
     * @return string
     */
    public function getGradeLetter($totalPoint): string
    {
        $totalPoint = floatval($totalPoint);
        if ($totalPoint >= 94) return "A";
        if ($totalPoint >= 90) return "A-";
        if ($totalPoint >= 87) return "B+";
        if ($totalPoint >= 84) return "B";
        if ($totalPoint >= 80) return "B-";
        if ($totalPoint >= 77) return "C+";
        if ($totalPoint >= 74) return "C";
        if ($totalPoint >= 70) return "C-";
        if ($totalPoint >= 67) return "D+";
        if ($totalPoint >= 64) return "D";
        if ($totalPoint >= 60) return "D-";
        if ($totalPoint >= 0) return "F";
        return "NG"; // Not Graded
    }

    public function registerSemester($student, $semester)
    {
        $year = $semester->year;
        $section = Section::find($student->section_id ?? $this->sectionId);

        $semesterLevel = $semester->level;

        $yearLevel = intval($year->name) - intval($section->year->name) + 1;

        // retrieve the courses that student is expected to take in the given year or semester(can still be dropped later if they dont want it)
        $courseOfferings = $section->courseOfferings()->where('semester_level', $semesterLevel)->where('year_level', $yearLevel)->get();

        if ($courseOfferings->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'No course offerings found for the student in the selected semester.']);
        }

        foreach ($courseOfferings as $courseOffering) {
            Enrollment::updateOrCreate([
                'student_id' => $student->id,
                'semester_id' => $semester->id,
                'course_offering_id' => $courseOffering->id,
                'status' => 'enrolled',
                'academic_status' => 'in_progress'
            ]);
        }

        // Set all previous semester_student records for this student to Inactive
        $semesterStudent = SemesterStudent::where('student_id', $student->id)
            ->where('academic_status', 'in_progress')
            ->update(['academic_status' => 'completed']);

        // Update the new/selected semester as Active for this student
        SemesterStudent::updateOrInsert(
            [
                'student_id' => $student->id,
                'semester_id' => $semester->id,
            ],
            [
                'academic_status' => 'in_progress',
                'payment_status' => 'unpaid',

                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        return true;
    }

    /**
     * Finds the semester for a given study mode, year, and course offering.
     *
     * @param mixed $studyMode
     * @param Year $year
     * @param CourseOffering $courseOffering
     * @return Semester|null
     */
    private function findSemester($studyMode, $year, $courseOffering)
    {
        $semester = $studyMode->semesters
            ->where('year_id', $year->id)
            ->where('level', $courseOffering->semester_level)
            ->first();
        
        if (!$semester) {
            $semesterExists = Semester::where('year_id', $year->id)
                                        ->where('level', $courseOffering->semester_level)
                                        ->first();
                                        
            if ($semesterExists) {

                DB::table('semester_study_mode')->insert([
                    'semester_id' => $semesterExists->id,
                    'study_mode_id' => $studyMode->id,
                    'start_date' => $semesterExists->start_date,
                    'end_date' => $semesterExists->end_date,
                    'status' => 'active',
                ]);
                $semester = $semesterExists;
            } else {
                $level = $courseOffering->semester_level;
                if ($level === 1) {
                    $name = '1st Semester of ' . $year->name;
                } elseif ($level === 2) {
                    $name = '2nd Semester of ' . $year->name;
                } elseif($level === 3) {
                    $name = '3rd Semester of ' . $year->name;
                } else {
                    $name = $level . 'th Semester of ' . $year->name;
                }

                $semester = Semester::create([
                    'year_id' => $year->id,
                    'level' => $courseOffering->semester_level,
                    'name' => $name,
                    'start_date' => Carbon::parse($year->start_date)->addMonths(4)->toDateString(),
                    'end_date' => Carbon::parse($year->start_date)->addMonths(7)->toDateString(), // Example end date
                    'status' => 'active',
                ]);
                DB::table('semester_study_mode')->insert([
                    'semester_id' => $semester->id,
                    'study_mode_id' => $studyMode->id,
                    'start_date' => Carbon::parse($semester->start_date)->toDateString(),
                    'end_date' => Carbon::parse($semester->end_date)->toDateString(),
                    'status' => 'active',
                ]);
            }
        }
        return Semester::where('year_id', $year->id)
            ->where('level', $courseOffering->semester_level)
            ->first();
    }
}
