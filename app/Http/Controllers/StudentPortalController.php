<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\Instructor;
use Inertia\Inertia;

class StudentPortalController extends Controller
{
    public function index()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return inertia('StudentPortal/Dashboard', [
            'student' => $student,
        ]);
    }

    public function courses()
    {
        $student = new StudentResource(request()->user()->student->load(['program', 'track', 'section', 'courses.courseOfferings' => function ($query) {
            dd($query->get());
        }]));

        return inertia('StudentPortal/Courses', [
            'student' => $student,
        ]);
    }

    public function show(Course $course)
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return Inertia::render('StudentPortal/CourseDetail', [
            'course' => $course,
            'student' => $student,
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
