<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ProgramResource;
use App\Models\Course;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = CourseResource::collection(Course::with('track')->paginate(15));

        return inertia('Courses/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = ProgramResource::collection(Program::with('tracks')->get());
        $programs = ProgramResource::collection(Program::with('tracks')->get());

        return inertia('Courses/Create', [
            'programs' => $programs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseStoreRequest $request)
    {
        $fields = $request->validated();

        $programsId = $fields['programs'] ?? [];
        unset($fields['programs']);

        $year = substr(Carbon::now()->year, -2);

        $course_id = 'CR'.'/'.str_pad(Course::count() + 1, 3, '0', STR_PAD_LEFT).'/'.$year;

        $fields['code'] = $course_id;

        $course = Course::create($fields);

        $course->programs()->syncWithPivotValues($programsId, ['is_common' => true]);

        $programs = Program::with('tracks', 'courses')->whereIn('id', $programsId)->get();

        foreach ($programs as $program) {
            $courses = $program->courses->pluck('id');
            foreach ($program->tracks as $track) {
                $track->courses()->syncWithPivotValues($courses, ['is_common' => true]);
            }
        }

        return redirect(route('courses.show', $course))->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course = $course->load('track');

        return inertia('Courses/Show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $course = $course->load('tracks', 'programs');
        $programs = ProgramResource::collection(Program::with('tracks')->get());

        return inertia('Courses/Edit', [
            'course' => new CourseResource($course),
            'programs' => $programs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseUpdateRequest $request, Course $course)
    {
        $fields = $request->validated();
        $programs = $fields['programs'] ?? [];
        $tracks = $fields['tracks'] ?? [];
        unset($fields['programs']);
        unset($fields['tracks']);

        // Optionally regenerate the course code if needed
        if (! $course->code) {
            $year = substr(Carbon::now()->year, -2);
            $course_id = 'CR'.'/'.str_pad(Course::count(), 3, '0', STR_PAD_LEFT).'/'.$year;
            $fields['code'] = $course_id;
        }

        $course->update($fields);
        $course->programs()->sync($programs);
        $course->tracks()->sync($tracks);

        return redirect(route('courses.show', $course))->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect(route('courses.index'))->with('success', 'Course deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $courses = Course::where('course_name', 'like', "%$search%")
            ->orWhere('course_id', 'like', "%$search%")
            ->latest()
            ->paginate(15);

        return Inertia::render('Courses/Index', compact('courses'));
    }
}
