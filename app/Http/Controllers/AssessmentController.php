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
        $section = Section::with(['user', 'program', 'department', 'students'])->findOrFail($section);

        $course = $section->courses()->with(['instructors', 'students'])->findOrFail($course);

        $semester = Semester::where('status', 'Active')->with('year')->firstOrFail(); // Current Active Semester

        $weights = $course->weights()
            ->where('semester_id', $semester->id)
            ->where('course_id', $course->id)
            ->where('section_id', $section->id)
            ->with('results')
            ->get();

        // Get instructor assigned to this course & section via pivot table

        $courseSectionAssignment = $course->sections()
            ->where('section_id', $section->id)
            ->first();

        $instructor = $courseSectionAssignment ? $courseSectionAssignment->pivot->instructor_id : null;
dd($instructor);
        return response()->json([
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
