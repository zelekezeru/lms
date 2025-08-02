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
use App\Models\Track;
use App\Models\StudyMode;
use App\Models\Enrollment;
use App\Models\Curriculum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception; // Use the base Exception class for general errors
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Not explicitly used but good to keep if headers are dynamic
use Maatwebsite\Excel\HeadingRowImport; // Not explicitly used but good to keep if headers are dynamic
use Maatwebsite\Excel\Imports\HeadingRowFormatter; // Not explicitly used but good to keep if headers are dynamic

class ResultsImport implements ToCollection
{
    protected $sectionId;
    protected $courseId;
    protected $results = [];
    protected $weights = [];

    public function __construct($courseId, $sectionId)
    {
        $this->courseId = $courseId;
        $this->sectionId = $sectionId;
    }

    /**
     * @param Collection $rows
     * @throws Exception
     */
    public function collection(Collection $rows)
    {
        // Ensure there's at least one row (for headings)
        if ($rows->isEmpty()) {
            return back()->withErrors('the uploaded file is empty');
        }

        // Get the first row as headings for weight points
        $weightHeadings = $rows[0]->toArray();
        
        // Slice the array to get only the columns representing weight points (starting from the 5th column, index 4)
        $weightPoints = array_slice($weightHeadings, 4);
        
        // Validate if weight points are found
        if (empty($weightPoints) || count($weightPoints) < 1) {
            return back()->withErrors('No weight points found in the file. Please ensure the header row contains weight percentages from the 5th column onwards.');
        }

        // Calculate the sum of the weight points
        $totalWeightSum = array_sum($weightPoints);
        
        // Validate if the total weight points sum to 100
        if ($totalWeightSum != 100) {
            return back()->withErrors('The total sum of weight points must be 100, but it is ' . $totalWeightSum . '.');
        }

        // Validate if the course offering exists

        // Process student rows, starting from the second row (index 1)
        $rows = $rows->slice(1);
        
        foreach ($rows as $row) {
            
            // Extract student ID number, trim whitespace
            $idNumber = trim($row[1] ?? '');
            $student = Student::where('id_no', $idNumber)->first();

            if (!$student) {
                continue; // Skip incomplete rows (student not found)
            }

            $section = $student ? $student->section: Section::find($this->sectionId);
            
            // Retrieve the CourseOffering to get associated course, section, instructor, etc.
            $courseOffering = CourseOffering::lookUpFor($this->courseId, $section->id);
            
            if($courseOffering == null)
            {   
                $track = Track::where('id', $section->track_id)->first();
                
                $course = Course::find($this->courseId);

                $curricula = $course->curricula;
                dd($track->id, $section->study_mode_id, $curricula);
                // Curriculum::where('course_id', $course->id)->where('track_id', $track->id)->where('study_mode_id', $section->study_mode_id)->get();

                dd($curriculum);
                $trackCourse = $course->with(['curricula' => function ($q) use ($track, $section) {
                    return $q->where('track_id', $track->id)->where('study_mode_id', $section->study_mode_id);
                }])->get();
                dd($trackCourse);
                // get the course with the id courseId

                $curriculum = $trackCourses->first()->curricula->first();

                $courseOffering = CourseOffering::updateOrCreate(
                    [
                        'course_id' => $trackCourses->first()->id,
                        'section_id' => $section->id,
                    ],
                    [
                        'year_level' => $curriculum->year_level ?? null,
                        'semester_level' => $curriculum->semester_level ?? null,
                    ],
                );
                dd($courseOffering);
            }
            // Validate year and semester levels are set for the course offering
            if (!$courseOffering->year_level || !$courseOffering->semester_level) {
                return back()->withErrors('Year level and semester level are not set for this course in this section.');
            }

            // Get the related models from the course offering
            $course = $courseOffering->course;
            $section = $courseOffering->section;
            // Use the instructor from course offering, or default to the first available instructor if not set
            $instructor = $courseOffering->instructor ?? Instructor::first();
            
            $studyMode = $courseOffering->section->studyMode;
            dd($studyMode);
            // Determine the academic year based on the section's year and course offering's year level
            $courseGivenAtYear = intval($section->year->name) + $courseOffering->year_level - 1;
            $year = Year::where('name', $courseGivenAtYear)->first();
            
            // Validate if the academic year exists
            if (!$year) {
                return back()->withErrors('A year with the name ' . $courseGivenAtYear . ' was not found. Please ensure the academic years are set up.');
            }

            // Find the semester based on study mode, year, and semester level
            $semester = $studyMode
                ->semesters()
                ->where('year_id', $year->id)
                ->where('level', $courseOffering->semester_level)
                ->first();

            // Validate if the semester exists
            if (!$semester) {
                return back()->withErrors('A semester with level ' . $courseOffering->semester_level . ' in the year ' . $year->name . ' is not applied for ' . $studyMode->name . '.');
            }
            
            // Start a database transaction for creating/updating weights
            DB::beginTransaction();
            try {
                // Iterate through the weight points from the header row to create/update Weight models
                $this->weights = $this->createWeights($weightPoints, $instructor, $section, $semester, $course);
                
                DB::commit(); // Commit the transaction for weights
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback if any error occurs during weight creation
                return redirect()->back()->withErrors('Error processing weights: ' . $e->getMessage());
            }

            // Sync the student's enrollment status for the current semester
            $student->semesters()->syncWithoutDetaching([
                $semester->id => [
                    'payment_status' => 'paid', // Default status, adjust as needed
                    'academic_status' => 'completed', // Default status, adjust as needed
                ]
            ]);

            // Student result create Method
            $totalPoint = $this->createResults($this->weights, $student, $course, $section, $year, $semester, $courseOffering, $row);
            dd($totalPoint); // Debugging point to check total points calculated
            // After processing results, calculate the total points for grading
            $this->createGrade($totalPoint, $student, $course, $section, $year, $semester, $courseOffering);
        } 
    }

    // Weights create method
    public function createWeights($weightPoints, $instructor, $section, $semester, $course): array
    {
       foreach ($weightPoints as $index => $weightPoint) {
                // Ensure the weight point is a valid number before using it
                if (!is_numeric($weightPoint)) {
                    // dd($weightPoint);
                    throw new Exception("Invalid weight point " . $weightPoint . " found at column " . ($index + 5) . " . Weight points must be numeric.");
                }

                $weight = Weight::updateOrCreate(
                    [
                        'name' => 'Weight ' . ($index + 1),
                        'instructor_id' => $instructor->id,
                        'section_id' => $section->id,
                        'semester_id' => $semester->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'point' => $weightPoint,
                        'description' => null, // You might want to get this from somewhere if available
                    ]
                );
                // Store weights by their index for easy lookup later when processing student scores
                $this->weights[$index] = $weight;
            }
        return $this->weights;
    }

    private function createResults($weights, $student, $course, $section, $year, $semester, $courseOffering, $row)
    {
        $totalPoint = 0; // Initialize total points for the current student

        // Validate if weights are set
        if (empty($weights)) {
            throw new Exception('No weights found. Please ensure weights are created before processing results.');
        }

        // Iterate through each weight point to process student scores
        foreach ($weights as $index => $weight) {
            // Assuming the score is in the column corresponding to the weight index + 5 (0-based index)
            $score = $row[$index + 5] ?? null; // Adjust based on your actual data structure

            // Validate if the score is numeric
            if (!is_numeric($score)) {
                continue; // Skip this weight if the score is not numeric
            }

            // Create or update the Result entry for the student
            Result::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'weight_id' => $weight->id,
                    'instructor_id' => $courseOffering->instructor_id ?? Auth::id(), // Use the course offering's instructor or the currently authenticated user
                ],
                [
                    'point' => floatval($score), // Ensure score is stored as a float
                    'description' => 'Imported from Excel', // You might want to get this from somewhere if available
                ]
            );

            // Add to total points for grade calculation
            $totalPoint += floatval($score) * ($weight->point / 100); // Adjust score by weight percentage
        }

        return $totalPoint; // Return the total points calculated for the student
    }

    private function createGrade($totalPoint, $student, $course, $section, $year, $semester, $courseOffering)
    {
        // Determine the grade letter based on the total calculated points
        $gradeLetter = $this->getGradeLetter($totalPoint);

        // Update or create the Grade entry for the student for this course and semester
        Grade::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'section_id' => $section->id,
                'year_id' => $year->id,
                'semester_id' => $semester->id,
            ],
            [
                'grade_point' => $totalPoint,
                'grade_letter' => $gradeLetter,
                'user_id' => Auth::id(), // Assign the currently authenticated user as the grader
                'grade_status' => 'Submitted',
                'grade_scale' => 100, // Assuming a 100-point scale
                'grade_description' => 'Excel Imported',
            ]
        );

        // Set academic status based on the grade letter
        $academicStatus = ($gradeLetter === 'F') ? 'failed' : 'completed';

        $enrollment = $student->enrollments()
            ->where('semester_id', $semester->id)
            ->where('course_offering_id', $courseOffering->id)
            ->first();

        if ($enrollment) {
            // Update existing enrollment status
            $enrollment->update([
                'academic_status' => $academicStatus,
            ]);
        } else {
            // No existing enrollment, create a new one
            Enrollment::updateOrCreate([
                'student_id' => $student->id,
                'course_offering_id' => $courseOffering->id,
                'semester_id' => $semester->id,
            ], [
                'status' => 'enrolled',
                'academic_status' => $academicStatus,
            ]);
        }
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
        if (is_nan($totalPoint)) return "N/A";
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
        return "F";
    }
}
