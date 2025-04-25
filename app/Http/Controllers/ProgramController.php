<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramStoreRequest;
use App\Http\Requests\ProgramUpdateRequest;
use App\Http\Resources\TrackResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\UserResource;
use App\Models\Track;
use App\Models\User;
use App\Models\Program;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Program::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query = Program::with('user')
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('language', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
        }

        $allowedSorts = ['name', 'language'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;

        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $programs = ProgramResource::collection($query->paginate(15));

        return inertia('Programs/Index', [
            'programs' => $programs, // Corrected to return the programs collection
            'sortInfo' => [
                "currentSortColumn" => $sortColumn,
                "currentSortDirection" => $sortDirection,
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        $program = new ProgramResource($program->load('tracks', 'director', 'studyModes'));

        return inertia('Programs/Show', [
            'program' => $program,
            'users' => UserResource::collection(User::all()),
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

        $program_id = 'PR' .  '-' . str_pad(Program::count() + 1, 2, '0', STR_PAD_LEFT) . '-' . $year;

        $fields['code'] = $program_id;

        // Assign the selected director the PROGRAM-DIRECTOR role
        $user = User::where('id', $fields['user_id'])->first();

        $user->assignRole('PROGRAM-DIRECTOR');

        $program = Program::create($fields);

        return redirect()->route('programs.show', $program)->with('success', 'Program created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $program->load('tracks', 'director');

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

        // Update the program record
        $program->update($fields);

        // Update the director's role if the user_id changes
        if (isset($fields['user_id'])) {
            $user = User::where('id', $fields['user_id'])->first();
            if ($user && !$user->hasRole('PROGRAM-DIRECTOR')) {
                $user->assignRole('PROGRAM-DIRECTOR');
            }
        }

        return redirect()->route('programs.show', $program)->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        // Delete all tracks linked to this program
        $program->tracks()->delete();

        $program->delete();

        return redirect()->route('programs.index')->with('success', 'Program deleted successfully.');
    }
}
