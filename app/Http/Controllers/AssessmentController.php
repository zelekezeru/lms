<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstructorResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Section;
use App\Models\Semester;

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

        $semester = Semester::where('status', 'Active')->first()->load(['year']); // Current Active semester

        $weights = $course->weights()->where('semester_id', $semester->id)->where('course_id', $course->id)->where('section_id', $section->id)->with('results')->get();

        $grades = $section->grades()->where('course_id', $course->id)->get();

        $students = StudentResource::collection($courseOffering->enrollments->pluck('student'));

        return inertia('Assessments/SectionCourse', [
            'section' => $section,
            'course' => $course,
            'semester' => $semester,
            'weights' => $weights,
            'instructor' => $instructor,
            'grades' => $grades,
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

        dd($student);

        return inertia('Assessments/SectionStudent', [
            'section' => $section,
            'student' => $student,
        ]);
    }
}
