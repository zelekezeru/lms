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
use App\Http\Resources\CourseResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\YearResource;
use App\Models\Course;
use Inertia\Inertia;
use Carbon\Carbon;

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
        $departments = DepartmentResource::collection(Department::all());

        $programs = ProgramResource::collection(Program::all());

        $years = YearResource::collection(Year::all()->sortBy('name', true));

        $semesters = SemesterResource::collection(Semester::all()->sortBy('name'));

        $users = UserResource::collection(User::all()->sortBy('name'));

        return Inertia::render('Sections/Create', [
            'departments' => $departments,
            'programs' => $programs,
            'years' => $years,
            'semesters' => $semesters,
            'users' => $users,
        ]);
    }
    

    public function store(SectionRequest $request)
    {   
        $fields = $request->validated();
        // Section code generation logic

        $year = substr(Carbon::now()->year, -2);

        $section_id = 'SC' . '-' . $year .  '-' . str_pad(Section::count() + 1, 2, '0', STR_PAD_LEFT);

        $fields['code'] = $section_id;
        
        $section = Section::create($fields);
    
        return redirect(route('sections.show', $section))->with('success', 'Section created successfully.');
    }

    public function show(Section $section)
    {
        $section->load(['user', 'program', 'department', 'year', 'semester', 'students', 'courses']);
        
        return Inertia::render('Sections/Show', [
            'section' => new SectionResource($section),
            'courses' => CourseResource::collection(Course::all()),
        ]);
    }
    
    public function update(SectionRequest $request, Section $section)
    {
        $fields = $request->validated();

        // Update the section record
        $section->update($fields);

        return redirect()->route('sections.show', $section)->with('success', 'Section updated successfully.');
    }

    public function edit(Section $section)
    {
        return Inertia::render('Sections/Edit', [
            'section' => $section->load(['user', 'program', 'department', 'year', 'semester']),
            'programs' => Program::all(),
            'departments' => Department::all(),
            'users' => User::all(),
            'years' => Year::all(),
            'semesters' => Semester::all(),
        ]);
    }
    

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->back()->with('success', 'Section deleted successfully.');
    }
}
