<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Http\Resources\DepartmentResource;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Http\Resources\ProgramResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CurriculumResource; // Ensure this class exists in the specified namespace
use App\Models\Course;
use App\Models\Curriculum;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Department::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%");
        }

        $allowedSorts = ['name', 'code', 'description'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;
        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }


        $departments = $query->paginate(15)->withQueryString();

        return inertia('Departments/Index', [
            'departments' => DepartmentResource::collection($departments),
            'programs' => ProgramResource::collection(Program::all()),
            'users' => UserResource::collection(User::all()),
            'search' => $request->search,
            'sortInfo' => [
                "currentSortColumn" => $sortColumn,
                "currentSortDirection" => $sortDirection,
            ]
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = UserResource::collection(User::all());

        $programs = ProgramResource::collection(Program::all());

        return inertia('Departments/Create', [
            'users' => $users,
            'programs' => $programs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentStoreRequest $request)
    {
        $fields = $request->validated();

        $year = substr(Carbon::now()->year, -2);

        $department_id = 'DP' .  '/' . str_pad(Department::count() + 1, 3, '0', STR_PAD_LEFT) . '/' . $year;

        $fields['code'] = $department_id;

        $department = Department::create($fields);

        // if the request containss a redirectTo parameter it sets the value of $redirectTo with that value but if it doesnt exist the departments.show method is the default
        $redirectTo = $request->input('redirectTo', route('departments.show', $department));

        return redirect($redirectTo)->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $department = new DepartmentResource($department->load('program', 'courses', 'sections', 'curriculums'));
        
        $courses = CourseResource::collection(Course::all());

        $curriculums = CurriculumResource::collection(Curriculum::all());
        
        return inertia('Departments/Show', [
            'department' => $department,
            'courses' => $courses,
            'curriculums' => $curriculums,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $department->load('program');

        return inertia('Departments/Edit', [
            'department' => new DepartmentResource($department),
            'users' => UserResource::collection(User::all()),
            'programs' => ProgramResource::collection(Program::all()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentUpdateRequest $request, Department $department)
    {
        $fields = $request->validated();

        // Optionally regenerate the department code if needed
        if (!$department->code) {
            $year = substr(Carbon::now()->year, -2);
            $department_id = 'DP' . '/' . str_pad(Department::count(), 3, '0', STR_PAD_LEFT) . '/' . $year;
            $fields['code'] = $department_id;
        }

        $department->update($fields);

        return redirect(route('departments.show', $department))->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect(route('departments.index'))->with('success', 'Department deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $departments = Department::where('department_name', 'like', "%$search%")
            ->orWhere('department_id', 'like', "%$search%")
            ->latest()
            ->paginate(15);
        return Inertia::render('Departments/Index', compact('departments'));
    }

    public function attach(Request $request, Department $department)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $department->curriculums()->sync($validated['courses']); // sync to update the curricula list

        return redirect()->back()->with('success', 'Courses assigned to curricula successfully.');
    }
}
