<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Http\Resources\DepartmentResource;
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
        $fields = $request->validated();

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
}
