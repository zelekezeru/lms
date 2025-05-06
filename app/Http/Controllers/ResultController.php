<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResultUpdateRequest;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $results = Result::with(['student', 'year', 'semester', 'section', 'course'])->paginate(30);

        return inertia('Results/Index', [
            'results' => $results,
        ]);
    }

    public function create()
    {
        return inertia('Results/Create');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $fields = $request->validate([
            'results' => 'required|array',
            'results.*.student_id' => 'required|integer',
            'results.*.weight_id' => 'required|integer',
            'results.*.point' => 'required|numeric|min:0',
        ]);

        $fields['instructor_id'] = Auth::id(); // Set the user_id to the authenticated user's ID

        foreach ($fields['results'] as $result) {
            Result::updateOrCreate(
                [
                    'student_id' => $result['student_id'],
                    'weight_id' => $result['weight_id'],
                    'instructor_id' => $fields['instructor_id'],
                ],
                [
                    'point' => $result['point'],
                    'description' => $result['description'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Results saved successfully!');
    }

    public function show(Result $result)
    {
        return inertia('Results/Show', [
            'result' => $result,
        ]);
    }

    public function edit(Result $result)
    {
        return inertia('Results/Edit', [
            'result' => $result,
        ]);
    }

    public function update(ResultUpdateRequest $request, Result $result)
    {
        $fields = $request->validated();

        $result->update($fields);

        return redirect()->route('results.index')->with('success', 'Result updated successfully.');
    }

    public function destroy(Result $result)
    {
        $result->delete();

        return redirect()->route('results.index')->with('success', 'Result deleted successfully.');
    }
}
