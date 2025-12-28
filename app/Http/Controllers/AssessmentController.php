<?php

namespace App\Http\Controllers;

use App\Http\Resources\CenterResource;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WeightResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\SectionResource;
use App\Models\Center;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Weight;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    // Section course Assessment
    public function section_course($section, $course)
    {
        $courseOffering = CourseOffering::lookUpFor($course, $section);
        
        // Check if the section and course exist
        if (! $courseOffering) {
            return redirect()->back()->with('error', 'No Such course Offering Found.');
        }
        $courseOffering->load( 'instructor', 'section.program', 'section.grades', 'enrollments.student');
        
        // dd($instructor);
        $section = $courseOffering->section;

        $course = $courseOffering->course;

        // Check if instructor exists
        if (! $courseOffering->instructor) {
            $instructor = null; // No instructor assigned
        } else{
            $instructor = $courseOffering->instructor;
        }
        
        $course = new CourseResource($course);
        $section = new SectionResource($section->load(['user', 'program', 'track', 'students', 'grades']));
        $semester = Semester::where('status', 'Active')->first()->load(['year']); // Current Active semester
        $weights = Weight::where('course_id', $course->id)->where('section_id', $section->id)->with('results')->get();
        $grades = $section->grades()->where('course_id', $course->id)->with('student')->get();

        $enrollments = $courseOffering->enrollments;
        
        $students = StudentResource::collection($courseOffering->enrollments->where('status', 'enrolled')->pluck('student')->sortBy('firstName'));
        
        $studentResults = [];
        // Fetch students with their course results
        foreach ($students as $student) {
            $studentResults[$student->id] = [];
            foreach ($weights as $weight) {
            $result = $weight->results->where('student_id', $student->id)->first();
            $studentResults[$student->id][$weight->id] = [
                'point' => $result ? $result->point : null,
                'description' => $result ? $result->description : null,
                'changed_point' => $result ? $result->changed_point : null,
                'instructor_id' => $result ? $result->instructor_id : null,
                'grade_id' => $result ? $result->grade_id : null,
                'student_id' => $student->id,
                'weight_id' => $weight->id,
                'changed_by' => $result ? $result->changed_by : null,
                'changed_at' => $result ? $result->changed_at : null,
            ];
            }
        }
        
        foreach ($weights as $weight) {
            foreach ($weight->results as $result) {
                if (!isset($studentResults[$result->student_id])) {
                    $studentResults[$result->student_id] = [];
                }
                if (!isset($studentResults[$result->student_id][$weight->id])) {
                    $studentResults[$result->student_id][$weight->id] = [
                        'point' => $result->point,
                        'description' => $result->description,
                        'changed_point' => $result->changed_point,
                        'instructor_id' => $result->instructor_id ?? null,
                        'grade_id' => $result->grade_id ?? null,
                        'student_id' => $result->student_id ?? null,
                        'weight_id' => $weight->id,
                        'changed_by' => $result->changed_by ?? null,
                        'changed_at' => $result->changed_at ?? null,
                    ];
                }
            }
        }

        $finalStudents = [];
        foreach ($studentResults as $studentId => $results) {
            $finalStudents[] = $studentId;
        }

        // Reorder $students to match the order in $finalStudents
        $students = Student::whereIn('id', $finalStudents)->get()->sortBy(function ($student) use ($finalStudents) {
            return array_search($student->id, $finalStudents);
        })->values();
        
        return inertia('Assessments/SectionCourse', [
            'section' => $section,
            'course' => $course,
            'semester' => $semester,
            'weights' => $weights,
            'grades' => $grades,
            'students' => $students,
            'instructor' => $instructor,
            'studentResults' => $studentResults,
            'courseOffering' => $courseOffering,
        ]);
    }

    // Center Course Assessment
    public function center_course($center, $course)
    {
        $center = Center::find($center);
        $course = Course::find($course);

        $students = $center->students()
            ->with(['user', 'grades' => function ($query) use ($course) {
                $query->where('course_id', $course->id);
            }])
            ->get();

        return inertia('Assessments/CenterCourse', [
            'center' => new CenterResource($center),
            'course' => new CourseResource($course),
            'students' => $students,
        ]);

    }

    public function section_student($section, $student)
    {
        $section = Section::find($section);

        $student = $section->students()->find($student);

        // Check if the section and student exist
        if (! $section || ! $student) {
            return redirect()->back()->with('error', 'Section or Student not found.');
        }

        return inertia('Assessments/SectionStudent', [
            'section' => $section,
            'student' => $student,
        ]);
    }
}
