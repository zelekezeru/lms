<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Instructor;
use Inertia\Inertia;

class StudentPortalController extends Controller
{
    public function index()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section', 'enrollments.courseOffering', 'enrollments.courseOffering.section.track', 'enrollments.courseOffering.section.studyMode'));

        return inertia('StudentPortal/Dashboard', [
            'student' => $student,
        ]);
    }

    public function enrollments()
    {
        $student = new StudentResource(request()->user()->student->load(['enrollments' => function ($q) {
            $q->where('status', 'Enrolled');
        }, 'enrollments.courseOffering']));
        return inertia('StudentPortal/Enrollments/Index', [
            'student' => $student,
        ]);
    }

    public function enrollmentDetail(Enrollment $enrollment)
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section.track', 'section.program'));

        return Inertia::render('StudentPortal/Enrollments/Show', [
            'enrollment' => new EnrollmentResource($enrollment->load('student', 'courseOffering.section.track.program', 'courseOffering.section.studyMode')),
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
