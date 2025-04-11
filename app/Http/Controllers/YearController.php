<?php

namespace App\Http\Controllers;

use App\Models\Year;
use App\Http\Requests\StoreYearRequest;
use App\Http\Requests\UpdateYearRequest;
use App\Http\Resources\YearResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class YearController extends Controller
{
    public function index(Request $request)
    {
        // Query the Year model
        $query = Year::query();
    
        // Search functionality
        if ($request->filled('search')) { // Check if 'search' is not empty
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%");
        }
    
        // Paginate the results
        $years = $query->paginate(15)->appends($request->query());
    
        // Return inertia view with data
        return inertia('Years/Index', [
            'years' => $years,
            'search' => $request->search, // Pass search term to the frontend
        ]);
    }
   
    
    public function create()
    {
        return Inertia::render('Years/Create');
    }

    public function store(StoreYearRequest $request)
    {
        $year = Year::create($request->validated());

        return redirect(route('years.show', $year))->with('success', 'Year created successfully.');
    }

    public function show(Year $year)
    {
        // Format the dates
        $year->created_at_formatted = \Carbon\Carbon::parse($year->created_at)->format('F j, Y'); 
        $year->updated_at_formatted = \Carbon\Carbon::parse($year->updated_at)->format('F j, Y'); 

        
        return inertia('Years/Show', [
            'year' => $year,
            'semesters' => $year->semesters,
        ]);
    }
    
    
    
    public function edit(Year $year)
    {
        return Inertia::render('Years/Edit', compact('year'));
    }

    public function update(UpdateYearRequest $request, Year $year)
    {
        $year->update($request->validated());

        return redirect(route('years.show', $year))->with('success', 'Year updated successfully.');
    }

    public function destroy(Year $year)
    {
        $year->delete();
        return redirect()->route('years.index')->with('success', 'Year deleted successfully.');
    }
}
