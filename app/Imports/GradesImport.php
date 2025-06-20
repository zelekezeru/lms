<?php
namespace App\Imports;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Course;
use App\Models\User;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Year;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GradesImport implements ToCollection, WithHeadingRow
{
    protected $context;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Get the student using the student_id_no (e.g., "SITS-0007-25")
            $student = Student::where('id_no', $row['student_id'])->first();

            if (!$student) continue;

            // Get course by code (e.g., "BCP408")
            $course = Course::where('code', $row['course_code'])->first();

            if (!$course) continue;

            $section = Section::where('id', $this->context['section_id'])->first(); // Make sure 'courses' is eager loaded

            $courses = Course::withExists([
                'courseOfferings as related_to_course_offering' => fn($q) =>
                    $q->where('section_id', $section->id)
            ])
            ->orderBy('name')
            ->orderByDesc('related_to_course_offering')
            ->get();
            
            // check if the course is found in the section Courses
            if (!$courses->contains($course)) {
                continue; // Skip if the course is not found in the section
            }
            
            // Ensure the student is enrolled in the section
            if (!$section->students->contains($student)) {
                continue; // Skip if the student is not enrolled in the section
            }
            // Ensuter the student has not already submitted a grade for this course in the current semester and year
            $existingGrade = Grade::where([
                'student_id' => $student->id,
                'course_id' => $course->id,
            ])->first();

            if ($existingGrade) {
                continue; // Skip if a grade already exists for this student and course
            }

            // If grade_point is provided and grade_letter is not, calculate grade_letter
            if (isset($row['grade_point']) && empty($row['grade_letter'])) {
                // Assuming a simple mapping for grade points to letters
                $row['grade_point'] = floatval($row['grade_point']);
                $point = $row['grade_point'];
                if (is_nan($point)) {
                    $row['grade_letter'] = 'N/A';
                } elseif ($point >= 94) {
                    $row['grade_letter'] = 'A';
                } elseif ($point >= 90) {
                    $row['grade_letter'] = 'A-';
                } elseif ($point >= 87) {
                    $row['grade_letter'] = 'B+';
                } elseif ($point >= 84) {
                    $row['grade_letter'] = 'B';
                } elseif ($point >= 80) {
                    $row['grade_letter'] = 'B-';
                } elseif ($point >= 77) {
                    $row['grade_letter'] = 'C+';
                } elseif ($point >= 74) {
                    $row['grade_letter'] = 'C';
                } elseif ($point >= 70) {
                    $row['grade_letter'] = 'C-';
                } elseif ($point >= 67) {
                    $row['grade_letter'] = 'D+';
                } elseif ($point >= 64) {
                    $row['grade_letter'] = 'D';
                } elseif ($point >= 60) {
                    $row['grade_letter'] = 'D-';
                } else {
                    $row['grade_letter'] = 'F';
                }
            }
            
            // If grade_scale is not provided, default to '0'
            elseif (empty($row['grade_point'])) {
                $row['grade_point'] = '0.00';
                $row['grade_letter'] = 'NG';
            }
            if (empty($row['grade_scale'])) {
                $row['grade_scale'] = '100';
            }

            $offeredSemester = $this->getSemesterCourseOfferedToSection($section, $course);

            if (!$offeredSemester) {                
                $this->context['semester_id'] = Semester::getActiveSemester()->id;
                $this->context['year_id'] = Semester::getActiveSemester()->year->id;             
            }
            else {
                $this->context['semester_id'] = $offeredSemester->id;

                $admissionYear = $offeredSemester ? $offeredSemester->year->name : null;
                $this->context['year_id'] = Year::where('name', $admissionYear)->first()->id;
            }

            Grade::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'semester_id' => $this->context['semester_id'],
                    'year_id' => $this->context['year_id'],
                ],
                [
                    'grade_point'       => $row['grade_point'],
                    'grade_letter'      => $row['grade_letter'],
                    'grade_scale'       => $row['grade_scale'],
                    'grade_description' => null,
                    'grade_complaint'   => false,
                    'grade_comment'     => null,
                    'changed_grade'     => null,
                    'grade_status'      => 'Submitted',
                    'changed_by'        => null,
                    'user_id'           => $this->context['user_id'],
                    'section_id'        => $this->context['section_id'],
                ]
            );
        }
    }

    function getSemesterCourseOfferedToSection(Section $section, Course $course)
    {
        $offering = $section->courseOfferings()->where('course_id', $course->id)->first();

        if (!$offering) {
            return null;
        }

        $semesterLevel = $offering->semester_level;
        $admissionYear = intval($section->year->name);
        $semesterYear = $admissionYear + intdiv($semesterLevel - 1, 2);

        return Semester::where('level', $semesterLevel)
            ->whereHas('year', fn($q) => $q->where('name', $semesterYear))
            ->first();
    }
    
}
