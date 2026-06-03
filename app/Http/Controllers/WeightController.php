<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeightStoreRequest;
use App\Http\Requests\WeightUpdateRequest;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Instructor;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Weight;
use App\Models\Year;
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

        // Enforce total weight ≤ 100 on backend
        $existingTotal = Weight::where('course_id', $fields['course_id'])
            ->where('section_id', $fields['section_id'])
            ->where('semester_id', $fields['semester_id'])
            ->sum('point');

        if ($existingTotal + floatval($fields['point']) > 100) {
            return back()->withErrors(['point' => 'Total assessment weights cannot exceed 100%.']);
        }

        $instructorId = CourseOffering::where('course_id', $fields['course_id'])
            ->where('section_id', $fields['section_id'])
            ->first()
            ?->instructor_id;
        $fields['instructor_id'] = $instructorId ?? Auth::id();

        Weight::create($fields);

        return redirect()->back()->with('success', 'Weight created successfully.');
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
        // Guard: cannot edit if results exist for this weight
        if ($weight->results()->count() > 0) {
            return back()->withErrors(['point' => 'Cannot edit this assessment weight because student results are already recorded against it. Delete the results first.']);
        }

        $fields = $request->validated();

        // Enforce total weight ≤ 100 on backend (exclude current weight from total)
        $otherTotal = Weight::where('course_id', $weight->course_id)
            ->where('section_id', $weight->section_id)
            ->where('semester_id', $weight->semester_id)
            ->where('id', '!=', $weight->id)
            ->sum('point');

        if ($otherTotal + floatval($fields['point']) > 100) {
            return back()->withErrors(['point' => 'Total assessment weights cannot exceed 100%.']);
        }

        $weight->update($fields);

        return redirect()->back()->with('success', 'Weight updated successfully.');
    }

    public function destroy(Weight $weight)
    {
        // Guard: cannot delete if results exist for this weight
        if ($weight->results()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete this assessment weight because student results are already recorded against it. Delete the results first.');
        }

        $weight->delete();

        return redirect()->back()->with('success', 'Weight deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $weights = Weight::where('name', 'like', "%{$query}%")
            ->orWhere('point', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with(['instructor', 'year', 'semester', 'course'])
            ->paginate(30);

        return inertia('Weights/Index', [
            'weights' => $weights,
        ]);
    }
}
