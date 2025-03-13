<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Http\Resources\DepartmentResource;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = DepartmentResource::collection(Department::paginate(10));

        return inertia('Departments/Index', [
            'departments' => $departments
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Departments/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentStoreRequest $request)
    {
        $tenant = 'SITS';

        $department_id = $tenant . '/' . 'DP' .  '/' . str_pad(Department::count() + 1, 4, '0', STR_PAD_LEFT);

        $fields = $request->validated();

        $fields['code'] = $department_id;
        
        $department = Department::create($fields);
        
        
        return redirect(route('departments.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return inertia('Departments/Show', [
            'department' => new DepartmentResource($department),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return inertia('Departments/Edit', [
            'department' => new DepartmentResource($department),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentUpdateRequest $request, Department $department)
    {
        $fields = $request->validated();
        
        $department->update($fields);

        return redirect(route('departments.index'));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();
        
        return redirect(route('departments.index'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $departments = Department::where('department_name', 'like', "%$search%")
            ->orWhere('department_id', 'like', "%$search%")
            ->latest()
            ->paginate(10);
        return Inertia::render('Departments/Index', compact('departments'));
    }
}
