<?php

namespace App\Http\Controllers;

use App\Models\Weight;
use Illuminate\Http\Request;

class WeightController extends Controller
{
    public function index()
    {
        $weights = Weight::with(['instructor', 'year', 'semester', 'course'])->get();

        return inertia('Weights/Index', [
            'weights' => $weights,
        ]);
    }
    public function create()
    {
        return inertia('Weights/Create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'weight_point' => 'required|numeric|min:0|max:100',
            'weight_description' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'year_id' => 'required|exists:years,id',
            'semester_id' => 'required|exists:semesters,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        Weight::create($request->all());

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
    public function update(Request $request, Weight $weight)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'weight_point' => 'required|numeric|min:0|max:100',
            'weight_description' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'year_id' => 'required|exists:years,id',
            'semester_id' => 'required|exists:semesters,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $weight->update($request->all());

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
            ->get();

        return inertia('Weights/Index', [
            'weights' => $weights,
        ]);
    }
}
