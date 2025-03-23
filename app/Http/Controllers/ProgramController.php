<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramStoreRequest;
use App\Http\Requests\ProgramUpdateRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\UserResource;
use App\Models\Department;
use App\Models\User;
use App\Models\Program;
use Carbon\Carbon;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = ProgramResource::collection(Program::with('user')->paginate(15));
        
        return inertia('Programs/Index', [
            'programs' => $programs,
        ]);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        $program = (Program::with('user', 'departments')->find($program->id));

        return inertia('Programs/Show', [
            'program' => $program,

        ]);
    }
    
    /**a
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = UserResource::collection(User::all());
        
        return  inertia('Programs/Create', [
            'users' => $users,
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(ProgramStoreRequest $request)
    {
        $fields = $request->validated();
        
        $year = substr(Carbon::now()->year, -2);

        $program_id = 'PR' .  '/' . str_pad(Program::count() + 1, 4, '0', STR_PAD_LEFT) . '/' . $year;  

        $fields['code'] = $program_id;
        
        $program = Program::create($fields);
        
        return inertia('Programs/Show', [
            'program' => new ProgramResource($program->load('user')),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $program->load('departments', 'user');

        return inertia('Programs/Edit', [
            'program' => new ProgramResource($program),
            'users' => UserResource::collection(User::all()),
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
