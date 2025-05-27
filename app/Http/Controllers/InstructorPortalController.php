<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\StudentResource;
use App\Models\CourseSectionAssignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Instructor;
use App\Models\Section;
use App\Models\Semester;

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
        $instructor = request()->user()->instructor;

        $instructor->load([
            'user',
            'courses' => function ($query) use ($instructor) {
                $query->withCount([
                    'courseSectionAssignments as sectionsCount' => function ($q) use ($instructor) {
                        $q->where('instructor_id', $instructor->id);
                    }
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
            'courseSectionAssignments.section.program',
            'courseSectionAssignments.section.track',
            'courseSectionAssignments.section.studyMode',
        ]);

        $uniqueAssignments = $instructor->courseSectionAssignments->unique('section_id');

        $instructor->setRelation('courseSectionAssignments', $uniqueAssignments);

        $instructor = new InstructorResource($instructor);

        return inertia('InstructorPortal/Sections', [
            'instructor' => $instructor,
        ]);
    }

    public function sectionDetail(Section $section)
    {
        // Check if the instructor actually teaches the course... we need to move this kinds of permission checking logics to InstructorPortalPolicies later 
        // if (!$course->instructors->contains(request()->user()->instructor->id)) {
        //     abort(403);
        // }

        $instructor = new InstructorResource(
            request()->user()->instructor->load('user', 'courses', 'courseSectionAssignments.section', 'courseSectionAssignments.course')
        );

        $section = new SectionResource($section->load([
            'courseSectionAssignments' => fn($q) => $q->where('instructor_id', $instructor->id),
            'courseSectionAssignments.course',
            'program',
            'track',
            'studyMode'
        ]));

        return Inertia::render('InstructorPortal/SectionDetail', [
            'section' => $section,
            'instructor' => $instructor,
        ]);
    }

    public function sectionCourseStudents(Section $section, Course $course)
    {
        $instructor = new InstructorResource(
            request()->user()->instructor->load('user')
        );

        $validInstructor = $instructor->courseSectionAssignments()->where('section_id', $section->id)->where('course_id', $course->id)->exists();
        if (! $validInstructor) {
            abort(403);
        }

        $students = StudentResource::collection($section->students);

        return inertia('InstructorPortal/SectionCoursePages/CourseStudents', [
            'students' => $students,
            'course' => $course,
            'section' => $section,
        ]);
    }

    public function sectionCourseAssessments($section, $course)
    {
        $section = Section::find($section)->load(['user', 'program', 'track', 'students', 'grades',]);

        $course = $section->courses()->find($course)->load(['instructors', 'students']);

        $semester = Semester::where('status', 'Active')->first()->load(['year']); // Current Active semester

        $weights = $course->weights()->where('semester_id', $semester->id)->where('course_id', $course->id)->where('section_id', $section->id)->with('results')->get();

        $instructor = $section->courses()->where('course_id', $course->id)->first()->pivot->instructor_id;

        $grades = $section->grades()->where('course_id', $course->id)->get();

        $students = $section->students()
                            ->whereHas('courses', function ($query) use ($course) {
                                $query->where('course_id', $course->id); 
                            })
                            ->orderBy('first_name')
                            ->orderBy('middle_name')
                            ->orderBy('last_name')
                            ->get();
                            
        // Load the instructor details
        if ($instructor) {
            $instructor = Instructor::find($instructor)->load(['user']);
        } else {
            $instructor = null;
        }

        // Check if the section and course exist
        if (! $section || ! $course) {
            return redirect()->back()->with('error', 'Section or Course not found.');
        }

        return inertia('InstructorPortal/SectionCoursePages/CourseAssessments', [
            'section' => $section,
            'course' => $course,
            'semester' => $semester,
            'weights' => $weights,
            'instructor' => $instructor,
            'grades' => $grades,
            'students' => $students,
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
