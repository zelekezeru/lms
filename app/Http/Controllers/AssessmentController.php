<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Instructor;
use App\Models\Section;
use App\Models\Semester;

class AssessmentController extends Controller
{
    public function section_course($section, $course)
    {
        $section = Section::find($section)->load(['user', 'program', 'track', 'students', 'grades']);

        $course = $section->courses()->find($course)->load(['instructors', 'students']);

        $semester = Semester::where('status', 'Active')->first()->load(['year']); // Current Active semester

        $weights = $course->weights()->where('semester_id', $semester->id)->where('course_id', $course->id)->where('section_id', $section->id)->with('results')->get();

        $instructor = $section->courses()->where('course_id', $course->id)->first()->pivot->instructor_id;

        $grades = $section->grades()->where('course_id', $course->id)->get();
        $students = StudentResource::collection($section->studentsByCourse($course->id));

        // Load the instructor details
        if ($instructor) {
            $instructor = $section->courses()->where('course_id', $course->id)->first()->pivot->instructor_id;

            $instructor = Instructor::find($instructor)->load(['user']);
        } else {
            $instructor = null;
        }

        // Check if the section and course exist
        if (! $section || ! $course) {
            return redirect()->back()->with('error', 'Section or Course not found.');
        }

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
