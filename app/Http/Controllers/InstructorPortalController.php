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
                'courseOfferings.section.semester',
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
            'courseOfferings.section.semester',
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
            'courseOfferings.section.semester',
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
            'classSchedules.courseOffering.course',
        ]);

        $instructor = new InstructorResource($instructor);

        return inertia('InstructorPortal/ClassSchedules', [
            'instructor' => $instructor,
        ]);
    }

    public function classSessions()
    {
        $instructor = request()->user()->instructor;

        $instructor->load([
            'classSessions.room',
            'classSessions.courseOffering.course',
        ]);
        $activeSemester = new SemesterResource(Semester::getActiveSemester());
        $instructor = new InstructorResource($instructor);

        return inertia('InstructorPortal/ClassSessions', [
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

        $courseOffering = CourseOffering::lookUpFor($course->id, $section->id)->load('enrollments.student', 'classSchedules.room', 'classSessions.room', 'classSessions.attendances.student');

        if (! CourseOffering::where('section_id', $section->id)->where('course_id', $course->id)->where('instructor_id', $instructor->id)->exists()) {
            abort(500);
        }

        $course = new CourseResource($course);
        $section = new SectionResource($section->load(['user', 'program', 'track', 'students', 'grades']));
        $semester = Semester::where('status', 'Active')->first()->load(['year']); // Current Active semester
        $weights = $course->weights()->where('semester_id', $semester->id)->where('course_id', $course->id)->where('section_id', $section->id)->with('results')->get();
        $grades = $section->grades()->where('course_id', $course->id)->get();

        $students = StudentResource::collection(
            $courseOffering->enrollments->where('status', 'enrolled')->pluck('student')
        );

        $activeSemester = $section->studyMode->activeSemester();
        $classSchedules = ClassScheduleResource::collection($courseOffering->classSchedules);
        $classSessions = ClassSessionResource::collection($courseOffering->classSessions);
        $rooms = RoomResource::collection(Room::all());

        return inertia('InstructorPortal/SectionCoursePages/SectionCourse', [
            'section' => $section,
            'course' => $course,
            'students' => $students,
            'semester' => $semester,
            'classSchedules' => $classSchedules,
            'classSessions' => $classSessions,
            'rooms' => $rooms,
            'weights' => $weights,
            'grades' => $grades,
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
