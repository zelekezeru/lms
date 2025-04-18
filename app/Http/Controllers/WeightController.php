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
        $sections = Section::all();

        return inertia('Weights/Create', [
            'instructors' => $instructors,
            'years' => $years,
            'semesters' => $semesters,
            'courses' => $courses,
        ]);
    }

    public function store(WeightStoreRequest $request)
    {
        $fields = $request->validated();
        dd($fields);
        Weight::create($fields);

        return redirect()->route('weights.index')->with('success', 'Weight created successfully.');
    }

    public function show(Weight $weight)
    {
        return inertia('Weights/Show', [
            'weight' => $weight,
        ]);
    }

    public function edit(Weight $weight)
    {
        return inertia('Weights/Edit', [
            'weight' => $weight,
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
            ->orWhere('weight_point', 'like', "%{$query}%")
            ->orWhere('weight_description', 'like', "%{$query}%")
            ->with(['instructor', 'year', 'semester', 'course'])
            ->paginate(30); // Ensure pagination is used

        return inertia('Weights/Index', [
            'weights' => $weights,
        ]);
    }
}
