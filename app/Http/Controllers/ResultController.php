<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $results = Result::with(['student', 'year', 'semester', 'section', 'course'])->get();

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
        $request->validate([
            'result_point' => 'required|string|max:255',
            'result_description' => 'nullable|string|max:255',
            'changed_point' => 'nullable|string|max:255',
            'weight_id' => 'required|exists:weights,id',
            'student_id' => 'required|exists:students,id',
            'year_id' => 'required|exists:years,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_id' => 'required|exists:sections,id',
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'required|exists:users,id',
            'grade_id' => 'required|exists:grades,id',
            'changed_by' => 'nullable|exists:users,id',
            'changed_at' => 'nullable|date',
        ]);

        Result::create($request->all());

        return redirect()->route('results.index')->with('success', 'Result created successfully.');
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
    public function update(Request $request, Result $result)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'result_point' => 'required|numeric|min:0|max:100',
            'result_description' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'year_id' => 'required|exists:years,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_id' => 'required|exists:sections,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $result->update($request->all());

        return redirect()->route('results.index')->with('success', 'Result updated successfully.');
    }
    public function destroy(Result $result)
    {
        $result->delete();

        return redirect()->route('results.index')->with('success', 'Result deleted successfully.');
    }
}
