<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use App\Models\Weight;
use App\Models\Semester;
use App\Models\Result;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ResultsImport implements ToCollection, WithHeadingRow
{
    protected $instructor_id;
    protected $results = [];

    public function __construct($instructor_id)
    {
        $this->instructor_id = $instructor_id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            
            $idNumber = trim($row['id_number'] ?? '');
            $courseCode = trim($row['course_code'] ?? '');

            if (!$idNumber || !$courseCode) {
                continue; // skip incomplete rows
            }

            $student = Student::where('id_no', $idNumber)->first();
            if (!$student) {
                // Optionally log missing student
                continue;
            }
            
            $course = Course::where('code', $courseCode)->first();
            if (!$course) {
                // Optionally log missing course
                continue;
            }
            
            foreach ($row->keys() as $header) {
                if (in_array($header, ['no', 'id_number', 'full_name', 'course_code'])) {
                    continue; // skip non-weight columns
                }
                
                $score = $row[$header];
                if (is_null($score) || $score === '') {
                    continue; // skip empty scores
                }
                
                $semester = Semester::where('status', 'Active')->first()->load(['year']);

                $weight = Weight::where('course_id', $course->id)
                    ->where('name', $header)
                    ->where('section_id', $student->section_id)
                    ->where('semester_id', $semester->id)
                    ->first();
                
                if (!$weight) {
                    $weight = Weight::create([
                        'name' => $header,
                        'point' => $header, // or map header to point if needed
                        'description' => null,
                        'instructor_id' => $this->instructor_id,
                        'section_id' => $student->section_id,
                        'semester_id' => $semester->id,
                        'course_id' => $course->id,
                    ]);
                }
                
                $result = Result::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'weight_id' => $weight->id,
                        'instructor_id' => $this->instructor_id,
                    ],
                    [
                        'point' => $score,
                        'description' => null,
                        'changed_point' => null,
                    ]
                );
                
                // Collect results in an array
                $this->results[] = $result;
            }
        }

        // Optionally, you can return the results array or process it further
        return $this->results;
    }
}
