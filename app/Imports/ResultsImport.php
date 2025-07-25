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
use Error;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

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

    public function collection(Collection $rows)
    {
        $weightHeadings = $rows[0]->toArray();

        // start from the 5th column to get the weight points
        $weightPoints = array_slice($weightHeadings, 4);
        
        // If there are no weight points, return an error
        if (empty($weightPoints || count($weightPoints) < 1)) {
            return redirect()->back()->withErrors('No weight points found in the file.');
        }

        // add up the total weight points
        $totalWeight = array_sum($weightPoints);
        // loop through the weight points to calculate the total weight
        if ($totalWeight != 100) {
            return redirect()->back()->withErrors('The total weight points must be 100, but now it is ' . $totalWeight);
        }

        // Get the course offering for the course and section to get access to section, course and instructor and also other important attributes like in when the course was given
        $courseOffering = CourseOffering::lookUpFor($this->courseId, $this->sectionId);

        if (! $courseOffering) {
            return redirect()->back()->withErrors('This Course is not given in this section');
        }

        if (! $courseOffering->year_level || ! $courseOffering->semester_level) {
            return redirect()->back()->withErrors('year level and semester level is not set for this course in this section');
        }
        // Get the course, section, instructor and study mode from the course offering
        $course = $courseOffering->course;
        $section = $courseOffering->section;
        $instructor = $courseOffering->instructor ? $courseOffering->instructor : Instructor::first();
        $studyMode = $courseOffering->section->studyMode;

        // If the course is not found redirect with error cuz the user might wanna create it
        $courseGivenAtYear = intval($section->year->name) + $courseOffering->year_level - 1; // This will give a year value in int eg: 2025, 2026
        $year = Year::where('name', $courseGivenAtYear)->first();

        // If the required Year is not found redirect with error cuz the user might wanna create it
        if (! $year) {
            return redirect()->back()->withErrors('A year with name ' . $courseGivenAtYear . ' was not found!');
        }
        // After we get the year we will pick a semester that belongs to the section's studyMode which have a level(1,2,3,4) as the semester_level the course should be given at
        $semester = $studyMode
            ->semesters()
            ->where('year_id', $year->id)
            ->where('level', $courseOffering->semester_level)
            ->first();

        // same as year warn the user to create the user before trying to import
        if (! $semester) {
            return redirect()->back()->withErrors('A semester with level ' . $courseOffering->semester_level . ' in the year' . $year->name . ' Is not applied for ' . $course->section->Study . '!');
        }

        $totalWeight = 0;
        DB::beginTransaction();

        try {
            foreach ($weightPoints as $index => $weightPoint) {

                if ($totalWeight > 100) {
                    throw new \Exception('The sum of the weights must be 100.');
                }
                
                $weight = Weight::create([
                    'name' => 'Weight ' . ($index + 1),
                    'point' => $weightPoint,
                    'description' => null,
                    'instructor_id' => $instructor->id,
                    'section_id' => $section->id,
                    'semester_id' => $semester->id,
                    'course_id' => $course->id,
                ]);

                $totalWeight += $weightPoint;
                // Map header to weight for later lookup
                $this->weights[$weightPoint] = $weight;
                
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors($e->getMessage());
        }

        // start rows from the 2nd row
        $rows = $rows->slice(1);
        // Get the semester in which that course will be given in the course offering of the section
        foreach ($rows as $row) {

            // Foreach rows

            // Make Sure that there are nowhicte spaces
            $idNumber = $row[1] ?? '';

            $idNumber = trim($idNumber);
            // retrieve the student and the course
            $student = Student::where('id_no', $idNumber)->first();
            
            // if there is no coursecode, idnumber or student(of the given id number) in a given row skip the row
            if (!$idNumber || !$student) {
                continue; // skip incomplete rows
            }
            
            // Get the score from the row, assuming it's in the 5th column (index 4)
            foreach ($weightPoints as $index => $weightPoint) {
                $score = $row[4 + $index] ?? 0;
                $weight = $this->weights[$weightPoint] ?? null;
                if (!$weight) {
                    continue;
                }
                $result = Result::create([
                        'student_id' => $student->id,
                        'weight_id' => $weight->id,
                        'instructor_id' => $instructor->id,
                        'point' => $score,
                        'description' => null,
                        'changed_point' => null,
                    ]);
                $this->results[] = $result;
            }
        }

        // Optionally, you can return the results array or process it further
        return $this->results;
    }
}
