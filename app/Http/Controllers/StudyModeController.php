<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudyModeStoreRequest;
use App\Http\Requests\StudyModeUpdateRequest;
use App\Http\Resources\StudyModeResource;
use App\Models\Department;
use App\Models\StudyMode;
use Illuminate\Http\Request;

class StudyModeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studyModes = StudyModeResource::collection(StudyMode::with('department')->paginate(15));

        return inertia('StudyModes/Index', compact('studyModes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        return inertia('StudyModes/Create', compact('departments'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StudyModeStoreRequest $request)
    {
        $fields = $request->validated();

        $studyMode = StudyMode::create($fields);
        
        $redirectTo = request()->query('redirectTo') ?? 'studyModes.index';
        $params = request()->query('params') ?? [];
        return redirect(route($redirectTo, $params));
    }
    
    /**
     * Display the specified resource.
     */
    public function show(StudyMode $studyMode)
    {
        $studyMode = new StudyModeResource($studyMode->load('department'));

        return inertia('StudyModes/Show', compact('studyMode'));

    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudyMode $studyMode)
    {
        $departments = Department::all();
        $studyMode = new StudyModeResource($studyMode->load('department'));

        return inertia('StudyModes/Edit', compact('departments', 'studyMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudyModeUpdateRequest $request, StudyMode $studyMode)
    {
        $fields = $request->validated();
        
        $studyMode->update($fields);

        return redirect(route('studyModes.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyMode $studyMode)
    {
        $studyMode->delete();
        
        return redirect(route('studyModes.index'));
    }
}
