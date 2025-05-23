<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Year;
use Illuminate\Http\Request;

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

        // Paginate the results
        $semesters = $query->orderBy('status', 'asc')
                        ->orderByDesc('start_date')
                        ->with('year')->paginate(15)->appends($request->query());

        // Return inertia view with data
        return inertia('Semesters/Index', [
            'semesters' => $semesters,
            'search' => $request->search, // Pass search term to the frontend
        ]);
    }

    public function show(Semester $semester)
    {
        // Format the dates
        $semester->created_at_formatted = \Carbon\Carbon::parse($semester->created_at)->format('F j, Y');
        $semester->updated_at_formatted = \Carbon\Carbon::parse($semester->updated_at)->format('F j, Y');

        return inertia('Semesters/Show', [
            'semester' => $semester,
            'year' => $semester->year,
        ]);
    }

    /**
     * Store a new semester for the assigned year.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|unique:semesters,name',
            'year_id' => 'required|exists:years,id',
            'status' => 'required|string|in:Active,Inactive',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_approved' => 'required|boolean',
            'is_completed' => 'required|boolean',
        ]);

        // Create the semester with the year_id
        $semester = Semester::create([
            'name' => $request->name,
            'status' => $request->status,
            'is_approved' => $request->is_approved,
            'is_completed' => $request->is_completed,
            'year_id' => $request->year_id,
        ]);

        $year = Year::find($request->year_id);

        // Redirect to the semester's show page
        return redirect()->back()->with('success', 'Semester created successfully.');
    }

    public function edit(Semester $semester)
    {
        $years = Year::all(); // Fetch all years for the dropdown

        return inertia('Semesters/Edit', [
            'semester' => $semester,
            'years' => $years,
        ]);
    }

    /**
     * Update an existing semester.
     */
    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'name' => 'required|string|unique:semesters,name,'.$semester->id,
            'year_id' => 'required|exists:years,id',
            'status' => 'required|string|in:Active,Inactive',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_approved' => 'required|boolean',
            'is_completed' => 'required|boolean',
        ]);

        // Update the semester record
        $semester->update($request->only(['name', 'year_id', 'status', 'start_date', 'end_date', 'is_approved', 'is_completed']));

        return redirect()->route('semesters.show', $semester)->with('success', 'Semester updated successfully.');
    }

    /**     * Delete a semester.
     */
    public function destroy(Semester $semester)
    {
        $semester->delete();

        return redirect()->route('semesters.index')->with('success', 'Semester deleted successfully.');

    }

    // Close Semester
    public function close(Semester $semester)
    {
        
    }
}
