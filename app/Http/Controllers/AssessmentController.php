<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function section_course($section, $course)
    {
        dd(Auth::user()->roles);

        $section = Section::find($section)->load(['user', 'program', 'department','students', 'courses']);
        
        $course = $section->courses()->find($course)->load(['instructors', 'students', 'sections', 'results', 'weights', 'grades']);
        
        $semester = Semester::where('status', "Active")->first()->load(['year']); // Current Active semester 

        // Check if the section and course exist
        if (!$section || !$course) {
            return redirect()->back()->with('error', 'Section or Course not found.');
        }

        return inertia('Assessments/SectionCourse', [
            'section' => $section,
            'course' => $course,
            'semester' => $semester, // Pass the semester to the view
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
