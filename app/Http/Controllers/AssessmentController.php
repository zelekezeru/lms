<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use App\Models\Instructor;

class AssessmentController extends Controller
{
    public function section_course($section, $course)
    {
        $section = Section::find($section)->load(['user', 'program', 'track', 'students']);

        $course = $section->courses()->find($course)->load(['instructors', 'students',]);

        $semester = Semester::where('status', "Active")->first()->load(['year']); // Current Active semester 

        $weights = $course->weights()->where('semester_id', $semester->id)->where('course_id', $course->id)->where('section_id', $section->id)->get()->load(['results']);


        $instructor = $section->courses()
            ->where('course_id', $course->id)
            ->first()->pivot->instructor_id;

        // Load the instructor details
        $instructor = Instructor::find($instructor)->load(['user']);

        // Check if the section and course exist
        if (!$section || !$course) {
            return redirect()->back()->with('error', 'Section or Course not found.');
        }

        return inertia('Assessments/SectionCourse', [
            'section' => $section,
            'course' => $course,
            'semester' => $semester,
            'weights' => $weights,
            'instructor' => $instructor,
        ]);
    }

    public function section_student($section, $student)
    {
        $section = Section::find($section);

        $student = $section->students()->find($student);

        // Check if the section and student exist
        if (!$section || !$student) {
            return redirect()->back()->with('error', 'Section or Student not found.');
        }

        dd($student);
        return inertia('Assessments/SectionStudent', [
            'section' => $section,
            'student' => $student,
        ]);
    }
}
