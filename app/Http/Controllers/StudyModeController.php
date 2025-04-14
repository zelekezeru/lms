<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudyModeStoreRequest;
use App\Http\Requests\StudyModeUpdateRequest;
use App\Http\Resources\StudyModeResource;
use App\Http\Resources\ProgramResource;
use App\Models\StudyMode;
use App\Models\Program;
use Illuminate\Http\Request;

class StudyModeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd('index');
        $studyModes = StudyModeResource::collection(StudyMode::with('program')->paginate(15));
dd($studyModes);
        return inertia('StudyModes/Index', compact('studyModes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = ProgramResource::collection(Program::all());
        
        return inertia('StudyModes/Create', [
            'programs'=> $programs,
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StudyModeStoreRequest $request)
    {
        $fields = $request->validated();
        
        $studyMode = StudyMode::create($fields);

        $program = Program::find($fields['program_id']);
        
        return redirect()->route('programs.show', $program)->with('success', 'Study Mode created successfully.');
    }
    
    /**
     * Display the specified resource.
     */
    public function show(StudyMode $studyMode)
    {
        $studyMode = new StudyModeResource($studyMode->load('program'));

        return inertia('StudyModes/Show', compact('studyMode'));

    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudyMode $studyMode)
    {
        $programs = ProgramResource::collection(Program::all());
        $studyMode = new StudyModeResource($studyMode->load('program')); // Load the related program

        return inertia('StudyModes/Edit', [
            'programs'=> $programs,
            'studyMode' => $studyMode,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudyModeUpdateRequest $request, StudyMode $studyMode)
    {
        $fields = $request->validated();

        // Update the study mode record
        $studyMode->update($fields);

        return redirect()->route('studyModes.show', $studyMode)->with('success', 'Study Mode updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyMode $studyMode)
    {
        $studyMode->delete();
        
        return redirect()->route('studyModes.index')->with('success', 'Study Mode deleted successfully.');
    }
}
