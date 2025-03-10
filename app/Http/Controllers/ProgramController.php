<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramStoreRequest;
use App\Http\Requests\ProgramUpdateRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProgramResource;
use App\Models\Department;
use App\Models\Program;
use App\Models\StudyMode;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = ProgramResource::collection(Program::paginate(10));

        return inertia('Programs/Index', [
            'programs' => $programs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = DepartmentResource::collection(Department::all());

        return  inertia('Programs/Create', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProgramStoreRequest $request)
    {
        $fields = $request->validated();

        $program = Program::create($fields);

        foreach ($request->studyModes as $studyMode) {
            $studyMode = StudyMode::create([
                'program_id' => $program->id,
                'mode' => $studyMode['mode'],
                'duration' => $studyMode['duration'],
                'fees' => $studyMode['fees']
            ]);
        }

        return redirect(route('programs.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        return inertia('Programs/Show', [
            'program' => new ProgramResource($program),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        return inertia('Programs/Edit', [
            'program' => new ProgramResource($program),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProgramUpdateRequest $request, Program $program)
    {
        $fields = $request->validated();

        $program->update($fields);

        return redirect(route('programs.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        // Later to be modified
        $program->delete();

        return redirect(route('programs.index'));
    }
}
