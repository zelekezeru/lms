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
use App\Models\Enrollment;
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
            throw new Exception('The uploaded file is empty.');
        }

        // Get the first row as headings for weight points
        $weightHeadings = $rows[0]->toArray();

        // Slice the array to get only the columns representing weight points (starting from the 5th column, index 4)
        $weightPoints = array_slice($weightHeadings, 4);

        // Validate if weight points are found
        if (empty($weightPoints) || count($weightPoints) < 1) {
            throw new Exception('No weight points found in the file. Please ensure the header row contains weight percentages from the 5th column onwards.');
        }

        // Calculate the sum of the weight points
        $totalWeightSum = array_sum($weightPoints);

        // Validate if the total weight points sum to 100
        if ($totalWeightSum != 100) {
            throw new Exception('The total sum of weight points must be 100, but it is ' . $totalWeightSum . '.');
        }

        // Retrieve the CourseOffering to get associated course, section, instructor, etc.
        $courseOffering = CourseOffering::lookUpFor($this->courseId, $this->sectionId);

        if (!$courseOffering) {
            throw new Exception('This Course is not offered in this section. Please ensure the course and section IDs are correct.');
        }

        // Validate year and semester levels are set for the course offering
        if (!$courseOffering->year_level || !$courseOffering->semester_level) {
            throw new Exception('Year level and semester level are not set for this course in this section.');
        }

        // Get the related models from the course offering
        $course = $courseOffering->course;
        $section = $courseOffering->section;
        // Use the instructor from course offering, or default to the first available instructor if not set
        $instructor = $courseOffering->instructor ?? Instructor::first();
        $studyMode = $courseOffering->section->studyMode;

        // Determine the academic year based on the section's year and course offering's year level
        $courseGivenAtYear = intval($section->year->name) + $courseOffering->year_level - 1;
        $year = Year::where('name', $courseGivenAtYear)->first();

        // Validate if the academic year exists
        if (!$year) {
            throw new Exception('A year with the name ' . $courseGivenAtYear . ' was not found. Please ensure the academic years are set up.');
        }

        // Find the semester based on study mode, year, and semester level
        $semester = $studyMode
            ->semesters()
            ->where('year_id', $year->id)
            ->where('level', $courseOffering->semester_level)
            ->first();

        // Validate if the semester exists
        if (!$semester) {
            throw new Exception('A semester with level ' . $courseOffering->semester_level . ' in the year ' . $year->name . ' is not applied for ' . $studyMode->name . '.');
        }

        // Start a database transaction for creating/updating weights
        DB::beginTransaction();
        try {
            // Iterate through the weight points from the header row to create/update Weight models
            foreach ($weightPoints as $index => $weightPoint) {
                // Ensure the weight point is a valid number before using it
                if (!is_numeric($weightPoint)) {
                    throw new Exception("Invalid weight point '{$weightPoint}' found at column " . ($index + 5) . ". Weight points must be numeric.");
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
            DB::commit(); // Commit the transaction for weights
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any error occurs during weight creation
            throw new Exception('Error processing weights: ' . $e->getMessage());
        }

        // Process student rows, starting from the second row (index 1)
        $rows = $rows->slice(1);

        // Start a new transaction for processing student results and grades
        // This outer transaction ensures that either all student data for a file is saved, or none is.
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                // Extract student ID number, trim whitespace
                $idNumber = trim($row[1] ?? '');
                
                // Skip rows that do not have a valid student ID number or if the student is not found
                if (empty($idNumber)) {
                    // Optionally log this skip for debugging
                    // \Log::info("Skipping row due to empty student ID number: " . json_encode($row->toArray()));
                    continue;
                }

                $student = Student::where('id_no', $idNumber)->first();

                if (!$student) {
                    // Optionally log this skip for debugging
                    // \Log::warning("Skipping row for ID number '{$idNumber}' as student not found: " . json_encode($row->toArray()));
                    continue; // Skip incomplete rows (student not found)
                }

                $totalPoint = 0; // Initialize total points for the current student

                // Sync the student's enrollment status for the current semester
                $student->semesters()->syncWithoutDetaching([
                    $semester->id => [
                        'payment_status' => 'paid', // Default status, adjust as needed
                        'academic_status' => 'completed', // Default status, adjust as needed
                    ]
                ]);

                // Iterate through each weight point to process student scores
                foreach ($weightPoints as $index => $weightPoint) {
                    // Check if the score column exists and is not null or empty
                    if (!isset($row[4 + $index]) || $row[4 + $index] === null || $row[4 + $index] === '') {
                        
                        continue;
                    }

                    // Get the score for the current weight point, default to 0 if not found (though the above check should prevent this)
                    $score = $row[4 + $index] ?? 0;

                    // Ensure the score is numeric
                    if (!is_numeric($score)) {
                        // Log or handle error for non-numeric score, then skip this specific score
                        Log::warning("Non-numeric score '{$score}' found for student ID '{$idNumber}' at column " . (4 + $index + 1) . ". Skipping this score.");
                        continue;
                    }

                    // Retrieve the corresponding Weight model using its index
                    $weight = $this->weights[$index] ?? null;
                    if (!$weight) {
                        // This should ideally not happen if weights were created successfully,
                        // but it's a safeguard. Log and skip if a weight is unexpectedly missing.
                        Log::error("Weight at index {$index} not found for student ID '{$idNumber}'. Skipping score.");
                        continue;
                    }

                    // Update or create the Result entry for the student, weight, and instructor
                    $result = Result::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'weight_id' => $weight->id,
                            'instructor_id' => $instructor->id,
                        ],
                        [
                            'point' => $score,
                            'description' => null,
                            'changed_point' => null,
                        ]
                    );
                    $this->results[] = $result; // Collect results if needed elsewhere

                    // Calculate total points for the student by adding the current score
                    $totalPoint = $totalPoint + ($score);
                }
                if ($totalPoint > 0) {

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

                    $courses = $course;

                    $coursesWithStatus = collect($courses)
                        ->mapWithKeys(function ($courseId) use ($student) {
                            return [$courseId => ['status' => 'Enrolled', 'section_id' => $student->section_id]];
                        })
                        ->toArray();
                        

                    $student->status()->update([
                        'is_enrolled' => true,
                        'enrolled_by_name' => Auth::user()->name,
                        'enrolled_at' => now(),
                    ]);
                        
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
                        $newEnrollment = Enrollment::create([
                            'semester_id' => $semester->id,
                            'course_offering_id' => $courseOffering->id,
                            'student_id' => $student->id,
                            'academic_status' => $academicStatus,
                            'status' => 'enrolled',
                        ]);
                    }
                } else {
                    // If total points are 0, we might want to log this or handle it differently
                    Log::info("No valid scores found for student ID '{$idNumber}'. No grade assigned.");
                }

            } // End of foreach ($rows as $row)

            DB::commit(); // Commit the transaction for student results, grades, and enrollments
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any error occurs during student processing
            throw new Exception('Error processing student results: ' . $e->getMessage());
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