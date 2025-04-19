<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeightStoreRequest;
use App\Http\Requests\WeightUpdateRequest;
use App\Models\Weight;
use App\Models\Instructor;
use App\Models\Year;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeightController extends Controller
{
    public function index()
    {
        $weights = Weight::with(['instructor', 'year', 'semester', 'course'])->paginate(30);

        return inertia('Weights/Index', [
            'weights' => $weights,
        ]);
    }
    
    public function create()
    {
        $instructors = Instructor::all();
        $years = Year::all();
        $semesters = Semester::all();
        $courses = Course::all();
        $sections = Section::with(['program'])->get();

        return inertia('Weights/Create', [
            'instructors' => $instructors,
            'years' => $years,
            'semesters' => $semesters,
            'courses' => $courses,
            'sections' => $sections,
        ]);
    }

    public function store(WeightStoreRequest $request)
    {
        $fields = $request->validated();
        
        $fields['instructor_id'] = Auth::id(); // Set the instructor_id to the authenticated user's ID
        
        $weight = Weight::create($fields);

        // if the request containss a redirectTo parameter it sets the value of $redirectTo with that value but if it doesnt exist the departments.show method is the default
        $redirectTo = $request->input('redirectTo', route('weights.show', $weight));

        return redirect($redirectTo)->with('success', 'Weight created successfully.');
    }

    public function show(Weight $weight)
    {
        return inertia('Weights/Show', [
            'weight' => $weight,
        ]);
    }

    public function edit(Weight $weight)
    {
        $instructors = Instructor::all();
        $years = Year::all();
        $semesters = Semester::get()->load('year');
        $courses = Course::all();
        $sections = Section::with(['program'])->get();

        $weight = $weight->load(['instructor', 'year', 'semester', 'course']);

        return inertia('Weights/Edit', [
            'weight' => $weight,
            'instructors' => $instructors,
            'years' => $years,
            'semesters' => $semesters,
            'courses' => $courses,
            'sections' => $sections,
        ]);
    }

    public function update(WeightUpdateRequest $request, Weight $weight)
    {
        $fields = $request->validated();

        $weight->update($fields);

        return redirect()->route('weights.index')->with('success', 'Weight updated successfully.');
    }

    public function destroy(Weight $weight)
    {
        $weight->delete();

        return redirect()->route('weights.index')->with('success', 'Weight deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $weights = Weight::where('name', 'like', "%{$query}%")
            ->orWhere('point', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with(['instructor', 'year', 'semester', 'course'])
            ->paginate(30); // Ensure pagination is used

        return inertia('Weights/Index', [
            'weights' => $weights,
        ]);
    }
}
