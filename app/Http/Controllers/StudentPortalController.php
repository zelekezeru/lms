<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\CourseSectionAssignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Instructor;

class StudentPortalController extends Controller
{
    public function index()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        // Instructor Assigned to Section
        $instructors = CourseSectionAssignment::where('section_id', $student->section->id)->whereNotNull('instructor_id')->with('instructor')->get();

        return inertia('StudentPortal/Dashboard', [
            'student' => $student,
        ]);
    }

    public function courses()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        $instructors = Instructor::whereHas('courses.sections', function ($query) use ($student) {
            $query->where('sections.id', $student->section_id);
        })->with('user')->get();


        return inertia('StudentPortal/Courses', [
            'student' => $student,
            'instructor' => $instructors,
        ]);
    }

    public function show(Course $course)
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return Inertia::render('StudentPortal/CourseDetail', [
            'course' => $course,
            'student' => $student
        ]);
    }

    public function profile()
    {

        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return inertia('StudentPortal/Profile', [
            'student' => $student,
        ]);
    }

    public function result()
    {

        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return inertia('StudentPortal/StudentResults', [
            'student' => $student,
        ]);
    }

    public function payment()
    {

        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return inertia('StudentPortal/Payment', [
            'student' => $student,
        ]);
    }

}
