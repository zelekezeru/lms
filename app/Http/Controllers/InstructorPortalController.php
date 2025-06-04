<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassScheduleResource;
use App\Http\Resources\ClassSessionResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Instructor;
use App\Models\Room;
use App\Models\Section;
use App\Models\Semester;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstructorPortalController extends Controller
{
    public function index()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load(
                'user',
                'courses',
                'courseOfferings.section.program',
                'courseOfferings.section.track',
                'courseOfferings.course'
            )
        );

        return inertia('InstructorPortal/Dashboard', [
            'instructor' => $instructor,
        ]);
    }

    public function courses()
    {
        $instructor = request()->user()->instructor;

        $instructor->load([
            'user',
            'courses' => function ($query) use ($instructor) {
                $query->withCount([
                    'courseOfferings as sectionsCount' => function ($q) use ($instructor) {
                        $q->where('instructor_id', $instructor->id);
                    },
                ]);
            },
        ]);

        $instructor = new InstructorResource($instructor);

        return inertia('InstructorPortal/Courses', [
            'instructor' => $instructor,
        ]);
    }

    public function courseDetail(Course $course)
    {
        // Check if the instructor actually teaches the course... we need to move this kinds of permission checking logics to InstructorPortalPolicies later
        if (! $course->instructors->contains(request()->user()->instructor->id)) {
            abort(403);
        }

        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseOfferings.section', 'courseOfferings.course')
        );

        $course = new CourseResource($course->load([
            'courseOfferings' => fn($q) => $q->where('instructor_id', $instructor->id),
            'courseOfferings.section',
            'courseOfferings.section.program',
            'courseOfferings.section.track',
            'courseOfferings.section.studyMode',
        ]));

        return Inertia::render('InstructorPortal/CourseDetail', [
            'course' => $course,
            'instructor' => $instructor,
        ]);
    }

    public function sections()
    {
        $instructor = request()->user()->instructor;

        $instructor->load([
            'user',
            'courseOfferings.section.program',
            'courseOfferings.section.track',
            'courseOfferings.section.studyMode',
        ]);

        $uniqueAssignments = $instructor->courseOfferings->unique('section_id');

        $instructor->setRelation('courseOfferings', $uniqueAssignments);

        $instructor = new InstructorResource($instructor);

        return inertia('InstructorPortal/Sections', [
            'instructor' => $instructor,
        ]);
    }

    public function classSchedules()
    {
        $instructor = request()->user()->instructor;

        $instructor->load([
            'classSchedules.room',
            'classSchedules.course',
        ]);

        $activeSemester = new SemesterResource(Semester::getActiveSemester());
        $instructor = new InstructorResource($instructor);

        return inertia('InstructorPortal/ClassSchedules', [
            'instructor' => $instructor,
            'activeSemester' => $activeSemester,
        ]);
    }

    public function sectionDetail(Section $section)
    {
        // Check if the instructor actually teaches the course... we need to move this kinds of permission checking logics to InstructorPortalPolicies later
        // if (!$course->instructors->contains(request()->user()->instructor->id)) {
        //     abort(403);
        // }

        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseOfferings.section', 'courseOfferings.course')
        );

        $section = new SectionResource($section->load([
            'courseOfferings' => fn($q) => $q->where('instructor_id', $instructor->id),
            'courseOfferings.course',
            'program',
            'track',
            'studyMode',
        ]));

        return Inertia::render('InstructorPortal/SectionDetail', [
            'section' => $section,
            'instructor' => $instructor,
        ]);
    }

    public function sectionCourse(Section $section, Course $course)
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user')
        );
        if (! CourseOffering::where('section_id', $section->id)->where('course_id', $course->id)->where('instructor_id', $instructor->id)->exists()) {
            abort(500);
        }

        $courseOffering = CourseOffering::lookUpFor($course->id, $section->id)->load('enrollments.student');
        
        $course = new CourseResource($course);
        $section = new SectionResource($section->load(['user', 'program', 'track', 'students', 'grades']));
        $semester = Semester::where('status', 'Active')->first()->load(['year']); // Current Active semester
        $grades = $section->grades()->where('course_id', $course->id)->get();
        $weights = $course->weights()->where('semester_id', $semester->id)->where('course_id', $course->id)->where('section_id', $section->id)->with('results')->get();
        // This fetches all students that learn $course in $section which means
        $students = StudentResource::collection($courseOffering->enrollments->pluck('student'));
        $activeSemester = Semester::getActiveSemester();
        $classSchedules = ClassScheduleResource::collection($course->classSchedules()->where('section_id', $section->id)->where('instructor_id', $instructor->id)->with('room')->get());
        $classSessions = ClassSessionResource::collection($course->classSessions()->where('section_id', $section->id)->where('instructor_id', $instructor->id)->with(['room', 'attendances.student'])->get());
        $rooms = RoomResource::collection(Room::all());

        return inertia('InstructorPortal/SectionCoursePages/SectionCourse', [
            'section' => $section,
            'course' => $course,
            'students' => $students,
            'semester' => $semester,
            'grades' => $grades,
            'classSchedules' => $classSchedules,
            'classSessions' => $classSessions,
            'rooms' => $rooms,
            'weights' => $weights,
            'instructor' => $instructor,
        ]);
    }

    public function profile()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseOfferings.section', 'courseOfferings.course')
        );

        return inertia('InstructorPortal/Profile', [
            'instructor' => $instructor,
        ]);
    }

    public function result()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseOfferings.section', 'courseOfferings.course')
        );

        // Example: fetch results/grades for instructor's courses
        $results = []; // You can populate this as needed

        return inertia('InstructorPortal/Results', [
            'instructor' => $instructor,
            'results' => $results,
        ]);
    }

    public function calendar()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseOfferings.section', 'courseOfferings.course')
        );

        // Example: fetch calendar info for instructor
        $calendars = []; // You can populate this as needed

        return inertia('InstructorPortal/calendar', [
            'instructor' => $instructor,
            'calendars' => $calendars,
        ]);
    }
}
