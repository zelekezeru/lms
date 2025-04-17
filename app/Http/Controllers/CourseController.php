<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use Carbon\Carbon;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\InstructorsResource;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Inertia\Inertia;
use App\Models\User;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = CourseResource::collection(Course::with('department')->paginate(15));
        
        return inertia('Courses/Index', [
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {           
        $programs = ProgramResource::collection(Program::all());
        return inertia('Courses/Create', [
            'programs' => $programs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseStoreRequest $request)
    {
        $fields = $request->validated();

        $programs = $fields['programs'] ?? [];
        unset($fields['programs']);
        $year = substr(Carbon::now()->year, -2);

        $course_id = 'CR' .  '/' . str_pad(Course::count() + 1, 3, '0', STR_PAD_LEFT) . '/' . $year;  

        $fields['code'] = $course_id;
        
        $course = Course::create($fields);
        
        $course->programs()->attach($programs);
        
        return redirect(route('courses.show', $course))->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course = $course->load('department');

        return inertia('Courses/Show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $departments = DepartmentResource::collection(\App\Models\Department::all());

        return inertia('Courses/Edit', [
            'course' => new CourseResource($course),
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseUpdateRequest $request, Course $course)
    {
        $fields = $request->validated();

        // Optionally regenerate the course code if needed
        if (!$course->code) {
            $year = substr(Carbon::now()->year, -2);
            $course_id = 'CR' . '/' . str_pad(Course::count(), 3, '0', STR_PAD_LEFT) . '/' . $year;
            $fields['code'] = $course_id;
        }

        $course->update($fields);

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
