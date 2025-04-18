<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function section_course($section, $course)
    {
        $section = Section::find($section)->load(['user', 'program', 'department', 'year', 'semester', 'students', 'courses']);
        
        $course = $section->courses()->find($course)->load(['instructors', 'students', 'sections', 'results', 'weights', 'grades']);
        
        // Check if the section and course exist
        if (!$section || !$course) {
            return redirect()->back()->with('error', 'Section or Course not found.');
        }

        return inertia('Assessments/SectionCourse', [
            'section' => $section,
            'course' => $course,
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
