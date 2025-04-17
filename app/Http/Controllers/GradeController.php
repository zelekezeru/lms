<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeStoreRequest;
use App\Http\Requests\GradeUpdateRequest;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with(['instructor', 'year', 'semester', 'course'])->paginate(30);

        return inertia('Grades/Index', [
            'grades' => $grades,
        ]);
    }

    public function create()
    {
        return inertia('Grades/Create');
    }

    public function store(GradeStoreRequest $request)
    {
        $request->validate([
        ]);
        $fields = $request->validated();

        Grade::create($fields);

        return redirect()->route('grades.index')->with('success', 'Grade created successfully.');
    }

    public function show(Grade $grade)
    {
        return inertia('Grades/Show', [
            'grade' => $grade,
        ]);
    }

    public function edit(Grade $grade)
    {
        return inertia('Grades/Edit', [
            'grade' => $grade,
        ]);
    }

    public function update(GradeUpdateRequest $request, Grade $grade)
    {
        $fields = $request->validated();
        
        $grade->update($fields);

        return redirect()->route('grades.index')->with('success', 'Grade updated successfully.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->route('grades.index')->with('success', 'Grade deleted successfully.');
    }
    public function search(Request $request)
    {
        $query = $request->input('query');

        $grades = Grade::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with(['instructor', 'year', 'semester', 'course'])
            ->get();

        return inertia('Grades/SearchResults', [
            'grades' => $grades,
        ]);
    }
}
