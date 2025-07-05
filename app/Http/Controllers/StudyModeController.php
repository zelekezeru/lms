<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudyModeStoreRequest;
use App\Http\Requests\StudyModeUpdateRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\StudyModeResource;
use App\Models\Program;
use App\Models\StudyMode;
use Illuminate\Http\Request;

class StudyModeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = StudyMode::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query = StudyMode::when($search, function ($query) use ($search) {
                $query->where('mode', 'like', "%{$search}%")
                    ->orWhere('duration', 'like', "%{$search}%")
                    ->orWhere('fees', 'like', "%{$search}%")
                    ->orWhereHas('program', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $allowedSorts = ['name'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;

        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $studyModes = StudyModeResource::collection($query->paginate(30));

        return inertia('StudyModes/Index', [
            'studyModes' => $studyModes, // Corrected to return the studyModes collection
            'sortInfo' => [
                'currentSortColumn' => $sortColumn,
                'currentSortDirection' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = ProgramResource::collection(Program::all());

        return inertia('StudyModes/Create', [
            'programs' => $programs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudyModeStoreRequest $request)
    {
        $fields = $request->validated();

        $studyMode = StudyMode::create($fields);

        return to_route('studyModes.show', $studyMode)->with('success', 'Study Mode created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudyMode $studyMode)
    {
        $studyMode = new StudyModeResource($studyMode->load('programs', 'students')); // Load the related program

        return inertia('StudyModes/Show', [
            'studyMode' => $studyMode,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudyMode $studyMode)
    {
        $programs = ProgramResource::collection(Program::all());

        $studyMode = new StudyModeResource($studyMode->load('programs')); // Load the related program

        return inertia('StudyModes/Edit', [
            'programs' => $programs,
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
        // Check if the study mode has associated programs or students
        if ($studyMode->programs()->count() > 0) {
            return redirect()->route('studyModes.index')->with('error', 'Cannot delete Study Mode with associated programs.');
        } elseif ($studyMode->students()->count() > 0) {
            return redirect()->route('studyModes.index')->with('error', 'Cannot delete Study Mode with associated students.');
        } elseif ($studyMode->status === 'active') {
            return redirect()->route('studyModes.index')->with('error', 'Cannot delete active Study Mode.');
        } else {
            // If no associated programs or students, delete the study mode
            $studyMode->programs()->detach(); // Detach all programs
            $studyMode->delete();

            return redirect()->route('studyModes.index')->with('success', 'Study Mode deleted successfully.');
        }
    }

    /**
     * Remove the specified program from the study mode.
     */
    public function destroyProgram(StudyMode $studyMode, Program $program)
    {
        $studyMode->programs()->detach($program->id);

        return redirect()->route('studyModes.show', $studyMode)
            ->with('success', 'Program removed from Study Mode successfully.');
    }
}
