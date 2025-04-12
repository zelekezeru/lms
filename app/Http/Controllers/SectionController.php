<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Year;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Requests\SectionRequest;
use Inertia\Inertia;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with(['user', 'program', 'department', 'year', 'semester'])->get();

        return Inertia::render('Sections/Index', [
            'sections' => $sections
        ]);
    }
    

    public function create()
    {
        return Inertia::render('Sections/Create', [
            'programs' => Program::all(),
            'departments' => Department::all(),
            'users' => User::all(),
        ]);
    }
    

    public function store(SectionRequest $request)
    {
        dd($request->validated());
        Section::create($request->validated());
    
        return redirect()->route('sections.index')->with('success', 'Section created successfully.');
    }
    
    public function update(SectionRequest $request, Section $section)
    {
        $section->update($request->validated());
    
        return redirect()->route('sections.index')->with('success', 'Section updated successfully.');
    }

    public function edit(Section $section)
    {
        return Inertia::render('Sections/Edit', [
            'section' => $section->load(['user', 'program', 'department', 'year', 'semester']),
            'programs' => Program::all(),
            'users' => User::all(),
            'departments' => Department::all(),
        ]);
    }
    

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->back()->with('success', 'Section deleted successfully.');
    }
}
