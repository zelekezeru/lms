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
        $semesters = $query->paginate(15)->appends($request->query());
        
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
            'is_approved' => 'required|boolean',
            'is_completed' => 'required|boolean',
        ]);
    
        // Create the semester with the year_id
        Semester::create([
            'name' => $request->name,
            'status' => $request->status,
            'is_approved' => $request->is_approved,
            'is_completed' => $request->is_completed,
            'year_id' => $request->year_id, // Ensure year_id is correctly assigned
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
            'status' => 'required|string|in:Active,Inactive',
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
        dd('hit' . $semester->id);
        $semester->delete();

        return redirect()->route('semesters.index')->with('success', 'Semester deleted successfully.');
    
    }
}
