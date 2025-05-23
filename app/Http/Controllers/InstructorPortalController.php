<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Resources\InstructorResource;
use App\Models\CourseSectionAssignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Instructor;

class InstructorPortalController extends Controller
{
    public function index()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseSectionAssignments.section', 'courseSectionAssignments.course')
        );

        return inertia('InstructorPortal/Dashboard', [
            'instructor' => $instructor,
        ]);
    }

    public function courses()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseSectionAssignments.section', 'courseSectionAssignments.course')
        );

        // Example: fetch sections the instructor is assigned to
        $courses = CourseResource::collection(
            $instructor->courses()
                ->with([
                    'courseSectionAssignments' => fn($q) => $q->where('instructor_id', $instructor->id),
                    'courseSectionAssignments.section.program',
                    'courseSectionAssignments.section.track',
                    'courseSectionAssignments.section.studyMode',
                ])
                ->get()
        );


        return inertia('InstructorPortal/Courses', [
            'instructor' => $instructor,
            'courses' => $courses,
        ]);
    }

    public function courseDetail(Course $course)
    {
        // Check if the instructor actually teaches the course... we need to move this kinds of permission checking logics to InstructorPortalPolicies later 
        if (!$course->instructors->contains(request()->user()->instructor->id)) {
            abort(403);
        }
        
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseSectionAssignments.section', 'courseSectionAssignments.course')
        );

        $course = new CourseResource($course->load([
                'courseSectionAssignments' => fn($q) => $q->where('instructor_id', $instructor->id),
                'courseSectionAssignments.section',
                'courseSectionAssignments.section.program',
                'courseSectionAssignments.section.track',
                'courseSectionAssignments.section.studyMode'
            ]));

        // Optionally, fetch more details about the course for the instructor
        return Inertia::render('InstructorPortal/CourseDetail', [
            'course' => $course,
            'instructor' => $instructor,
        ]);
    }

    public function profile()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseSectionAssignments.section', 'courseSectionAssignments.course')
        );

        return inertia('InstructorPortal/Profile', [
            'instructor' => $instructor,
        ]);
    }

    public function result()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseSectionAssignments.section', 'courseSectionAssignments.course')
        );

        // Example: fetch results/grades for instructor's courses
        $results = []; // You can populate this as needed

        return inertia('InstructorPortal/Results', [
            'instructor' => $instructor,
            'results' => $results,
        ]);
    }

    public function schedule()
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseSectionAssignments.section', 'courseSectionAssignments.course')
        );

        // Example: fetch schedule info for instructor
        $schedules = []; // You can populate this as needed

        return inertia('InstructorPortal/schedule', [
            'instructor' => $instructor,
            'schedules' => $schedules,
        ]);
    }
}
