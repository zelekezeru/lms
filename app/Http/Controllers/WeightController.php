<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeightStoreRequest;
use App\Http\Requests\WeightUpdateRequest;
use App\Models\Weight;
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
        return inertia('Weights/Create');
    }

    public function store(WeightStoreRequest $request)
    {
        $fields = $request->validated();

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
