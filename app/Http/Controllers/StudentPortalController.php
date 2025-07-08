<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassScheduleResource;
use App\Http\Resources\ClassSessionResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WeightResource;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Weight;
use Inertia\Inertia;

class StudentPortalController extends Controller
{
    public function index()
    {
        $student = new StudentResource(request()->user()->student->load(
            'program',
            'track',
            'section',
            'enrollments.courseOffering',
            'enrollments.courseOffering.section.track',
            'enrollments.courseOffering.section.studyMode'
        ));

        return inertia('StudentPortal/Dashboard', [
            'student' => $student,
        ]);
    }

    public function enrollments()
    {
        $student = new StudentResource(request()->user()->student->load([
            'enrollments',
            'enrollments.courseOffering',
            'grades',
        ]));

        return inertia('StudentPortal/Enrollments/Index', [
            'student' => $student,
        ]);
    }

    public function enrollmentDetail(Enrollment $enrollment)
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section.track', 'section.program', 'results'));
        $enrollment->load('student', 'courseOffering.course', 'courseOffering.section.track.program', 'courseOffering.section.studyMode');

        $course = $enrollment->courseOffering->course;
        $section = $enrollment->courseOffering->section;

        $weights = WeightResource::collection(Weight::where('section_id', $section->id)->where('course_id', $course->id)->with('results', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get());

        $classSchedules = ClassScheduleResource::collection($enrollment->courseOffering->classSchedules);
        $classSessions = ClassSessionResource::collection($enrollment->courseOffering->classSessions);

        $activeSemester = $enrollment->semester;

        return Inertia::render('StudentPortal/Enrollments/Show', [
            'enrollment' => new EnrollmentResource($enrollment),
            'student' => $student,
            'weights' => $weights,
            'classSchedules' => $classSchedules,
            'classSessions' => $classSessions,
            'activeSemester' => $activeSemester,
        ]);
    }

    public function classSchedules()
    {
        $studentModel = request()->user()->student->load([
            'program',
            'track',
            'section',
            'enrollments.courseOffering.classSchedules',
        ]);

        $student = new StudentResource($studentModel);

        $classSchedules = ClassScheduleResource::collection($studentModel->classSchedules());

        $activeSemester = new SemesterResource(Semester::getActiveSemester());

        return inertia('StudentPortal/ClassSchedules', [
            'student' => $student,
            'classSchedules' => $classSchedules,
            'activeSemester' => $activeSemester,
        ]);
    }

    public function classSessions()
    {
        $studentModel = request()->user()->student->load([
            'program',
            'track',
            'section',
            'enrollments.courseOffering.classSessions',
        ]);

        $student = new StudentResource($studentModel);

        $classSessions = ClassSessionResource::collection($studentModel->classSessions());

        $activeSemester = new SemesterResource(Semester::getActiveSemester());

        return inertia('StudentPortal/ClassSessions', [
            'student' => $student,
            'classSessions' => $classSessions,
            'activeSemester' => $activeSemester,
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

    public function editProfile()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return inertia('profile/StudentEdit', [
            'student' => $student,
        ]);
    }
}
