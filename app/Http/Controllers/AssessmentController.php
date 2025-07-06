<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstructorResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WeightResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\SectionResource;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Weight;

class AssessmentController extends Controller
{
    public function section_course($section, $course)
    {
        $courseOffering = CourseOffering::lookUpFor($course, $section)->load('instructor', 'section.program', 'section.grades', 'enrollments.student');
        // Check if the section and course exist
        if (! $courseOffering) {
            return redirect()->back()->with('error', 'No Such course Offering Found.');
        }

        $section = $courseOffering->section;

        $course = $courseOffering->course;

        $instructor = new InstructorResource($courseOffering->instructor);
        $course = new CourseResource($course);
        $section = new SectionResource($section->load(['user', 'program', 'track', 'students', 'grades']));
        $semester = Semester::where('status', 'Active')->first()->load(['year']); // Current Active semester
        $weights = $course->weights()->where('semester_id', $semester->id)->where('course_id', $course->id)->where('section_id', $section->id)->with('results')->get();
        $grades = $section->grades()->where('course_id', $course->id)->get();

        $enrollments = $courseOffering->enrollments;

        $students = StudentResource::collection(
            $courseOffering->enrollments->where('status', 'enrolled')->orderBy('firstName', 'asc')->pluck('student')
        );
        
        $studentResults = [];
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
        
        return inertia('Assessments/SectionCourse', [
            'section' => $section,
            'course' => $course,
            'semester' => $semester,
            'instructor' => $instructor,
            'weights' => $weights,
            'grades' => $grades,
            'students' => $students,
            'studentResults' => $studentResults,
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
