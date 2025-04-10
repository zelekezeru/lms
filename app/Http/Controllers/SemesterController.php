<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Year;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Store a new semester for the assigned year.
     */
    public function store(Request $request, Year $year)
    {
        $request->validate([
            'name' => 'required|string|unique:semesters,name',
            'status' => 'required|string|in:active,inactive',
            'is_approved' => 'required|boolean',
            'is_completed' => 'required|boolean',
        ]);
    
        // Create the semester with the year_id
        Semester::create([
            'name' => $request->name,
            'status' => $request->status,
            'is_approved' => $request->is_approved,
            'is_completed' => $request->is_completed,
            'year_id' => $year->id, // Ensure year_id is correctly assigned
        ]);
    
        return redirect()->back()->with('success', 'Semester created successfully.');
    }
    
    
    /**
     * Update an existing semester.
     */
    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'name' => 'required|string|unique:semesters,name,' . $semester->id,
            'status' => 'required|string|in:active,inactive',
            'is_approved' => 'required|boolean',
            'is_completed' => 'required|boolean',
        ]);

        $semester->update($request->all());

        return redirect()->back()->with('success', 'Semester updated successfully.');
    }

    /**
     * Delete a semester.
     */
    public function destroy(Semester $semester)
    {
        $semester->delete();

        return redirect()->back()->with('success', 'Semester deleted successfully.');
    }
}
