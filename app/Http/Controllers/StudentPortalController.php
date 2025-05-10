<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\CourseSectionAssignment;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function index()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        // Instructor Assigned to Section
        $instructors = CourseSectionAssignment::where('section_id', $student->section->id)->whereNotNull('instructor_id')->with('instructor')->get();

        dd($instructors);

        return inertia('StudentPortal/Dashboard', [
            'student' => $student,
        ]);
    }

    public function courses()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return inertia('StudentPortal/Courses', [
            'student' => $student,
        ]);
    }
}
