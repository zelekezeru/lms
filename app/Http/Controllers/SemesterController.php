<?php

namespace App\Http\Controllers;

use App\Http\Resources\SemesterResource;
use App\Http\Resources\StudyModeResource;
use App\Http\Resources\YearResource;
use App\Models\Semester;
use App\Models\StudyMode;
use App\Models\Year;
use Carbon\Carbon;
use Database\Seeders\YearSeeder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        // Query the Semester model
        $query = Semester::query();

        // Search functionality
        if ($request->filled('search')) { // Check if 'search' is not empty
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('status', 'LIKE', "%{$search}%");
        }

        $allowedSorts = ['name', 'level'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;

        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $semesters = SemesterResource::collection($query->with('year')->paginate(30));

        return inertia('Semesters/Index', [
            'semesters' => $semesters, // Corrected to return the semesters collection
            'sortInfo' => [
                'currentSortColumn' => $sortColumn,
                'currentSortDirection' => $sortDirection,
            ],
        ]);
    }

    public function show(Semester $semester)
    {
        // Format the dates
        $semester = new SemesterResource($semester->load('year', 'studyModes'));
        $studyModes = StudyModeResource::collection(StudyMode::all());

        return inertia('Semesters/Show', [
            'semester' => $semester,
            'studyModes' => $studyModes
        ]);
    }

    public function create()
    {
        $years = YearResource::collection(Year::all());
        $studyModes = StudyModeResource::collection(StudyMode::all());

        return inertia('Semesters/Create', compact('years', 'studyModes'));
    }
    /**
     * Store a new semester for the assigned year.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:semesters,name',
            'level' => 'required|integer',
            'year_id' => 'required|exists:years,id',
            'start_date' => 'required',
            'end_date' => 'required|after:start_date',
        ]);

        if (Semester::where('year_id', $validated['year_id'])->where('level', $validated['level'])->exists()) {
            return redirect()->back()->withErrors(['level' => 'There is Already A semester in the given year with level ' . $validated['level']]);
        }


        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);

        // Replace fields
        $validated['start_date'] = $start->toDateString();         // YYYY-MM-DD
        $validated['end_date'] = $end->toDateString();         // YYYY-MM-DD
        // Create the semester with the year_id
        $semester = Semester::create($validated);


        $studyModesData = [];

        foreach ($request->study_modes as $studyModeId => $data) {
            $studyModesData[$studyModeId] = [
                'start_date' => Carbon::parse($data['start_date'])->format('Y-m-d H:i:s'),
                'end_date' => Carbon::parse($data['end_date'])->format('Y-m-d H:i:s'),
            ];
        }

        $semester->studyModes()->sync($studyModesData);

        // Redirect to the semester's show page
        return to_route('semesters.show', $semester)->with('success', 'Semester created successfully.');
    }

    public function edit(Semester $semester)
    {
        $semester = new SemesterResource($semester->load('year', 'studyModes'));
        $studyModes = StudyModeResource::collection(StudyMode::all());
        $years = YearResource::collection(Year::all()); // Fetch all years for the dropdown

        return inertia('Semesters/Edit', [
            'semester' => $semester,
            'years' => $years,
            'studyModes' => $studyModes,
        ]);
    }

    /**
     * Update an existing semester.
     */
    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique('semesters')->ignore($semester->id)],
            'level' => 'required|integer',
            'year_id' => 'required|exists:years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $semesterWithSameLevel = Semester::where('year_id', $validated['year_id'])->where('level', $validated['level']);

        if ($semesterWithSameLevel->exists() && $semesterWithSameLevel->first()->id !== $semester->id) {
            return redirect()->back()->withErrors('level', 'There is Already A semester in the given year with level ' . $validated['level']);
        }


        // Update the semester record
        $semester->update(
            $validated
        );


        $studyModesData = [];

        foreach ($request->study_modes as $studyModeId => $data) {
            $studyModesData[$studyModeId] = [
                'start_date' => Carbon::parse($data['start_date'])->format('Y-m-d H:i:s'),
                'end_date' => Carbon::parse($data['end_date'])->format('Y-m-d H:i:s'),
            ];
        }

        $semester->studyModes()->sync($studyModesData);

        return redirect()->route('semesters.show', $semester)->with('success', 'Semester updated successfully.');
    }

    /**     * Delete a semester.
     */
    public function destroy(Semester $semester)
    {
        // Check if the semester is active or has associated data
        if ($semester->status === 'active') {
            return back()->withErrors('Not Allowed', 'Cannot delete an active semester.');
        } elseif ($semester->status === 'archived') {
            return back()->withErrors('Not Allowed', 'Cannot delete an archived semester.');
        } elseif ($semester->students()->count() > 0) {
            return back()->withErrors('Not Allowed', 'Cannot delete a semester with associated students.');
        } elseif ($semester->courses()->count() > 0) {
            return back()->withErrors('Not Allowed', 'Cannot delete a semester with associated courses.');
        } elseif ($semester->results()->count() > 0) {
            return back()->withErrors('Not Allowed', 'Cannot delete a semester with associated results.');
        } elseif ($semester->attendance()->count() > 0) {
            return back()->withErrors('Not Allowed', 'Cannot delete a semester with associated attendance records.');
        } elseif ($semester->grades()->count() > 0) {
            return back()->withErrors('Not Allowed', 'Cannot delete a semester with associated grades.');
        } elseif ($semester->payments()->count() > 0) {
            return back()->withErrors('Not Allowed', 'Cannot delete a semester with associated payments.');
        } elseif ($semester->schedules()->count() > 0) {
            return back()->withErrors('Not Allowed', 'Cannot delete a semester with associated schedules.');
        } else {
            // If all checks pass, delete the semester
            $semester->delete();

            return redirect()->route('semesters.index')->with('success', 'Semester deleted successfully.');
        }
    }

    public function syncStudyModes(Request $request, Semester $semester)
    {

        $studyModesData = [];

        foreach ($request->study_modes as $studyModeId => $data) {
            $studyModesData[$studyModeId] = [
                'start_date' => Carbon::parse($data['start_date'])->format('Y-m-d H:i:s'),
                'end_date' => Carbon::parse($data['end_date'])->format('Y-m-d H:i:s'),
            ];
        }

        $semester->studyModes()->sync($studyModesData);

        return redirect()->back()->with('success', 'Changes Applied Successfully');
    }
}
