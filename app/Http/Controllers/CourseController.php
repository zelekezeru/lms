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
use App\Http\Resources\InstructorsResource;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Department;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = CourseResource::collection(Course::paginate(15));
        
        return inertia('Courses/Index', [
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        $users = UserResource::collection(User::all());

        $departments = DepartmentResource::collection(Department::all());
        
        return inertia('Courses/Create', [
            'users' => $users,
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseStoreRequest $request)
    {
        $fields = $request->validated();
        
        $year = substr(Carbon::now()->year, -2);

        $course_id = 'DP' .  '/' . str_pad(Course::count() + 1, 4, '0', STR_PAD_LEFT) . '/' . $year;  

        $fields['code'] = $course_id;
        
        $course = Course::create($fields);
        
        
        return redirect(route('courses.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course = (Course::with('department')->find($course->id));

        $instructors = InstructorsResource::collection(Course::with('')->find($course->id));

        return inertia('Courses/Show', [
            'course' => $course,
            'instructors' => $instructors,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $course->load('department');

        return inertia('Courses/Edit', [
            'course' => new CourseResource($course),

            'users' => UserResource::collection(User::all()),
            'departments' => DepartmentResource::collection(Department::all()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseUpdateRequest $request, Course $course)
    {
        $fields = $request->validated();
        
        $course->update($fields);

        return redirect(route('Courses.index'));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        
        return redirect(route('courses.index'));
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
