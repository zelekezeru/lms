<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionStoreRequest;
use App\Http\Requests\SectionUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\TrackResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\YearResource;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Track;
use App\Models\User;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with(['user', 'program', 'track', 'year', 'semester'])->get();

        return Inertia::render('Sections/Index', [
            'sections' => $sections,
        ]);
    }

    public function create()
    {
        $tracks = TrackResource::collection(Track::all());

        $programs = ProgramResource::collection(Program::all());

        $years = YearResource::collection(Year::all()->sortBy('name', true));

        $semesters = SemesterResource::collection(Semester::all()->sortBy('name'));

        $users = UserResource::collection(User::all()->sortBy('name'));

        return Inertia::render('Sections/Create', [
            'tracks' => $tracks,
            'programs' => $programs,
            'years' => $years,
            'semesters' => $semesters,
            'users' => $users,
        ]);
    }

    public function store(SectionStoreRequest $request)
    {
        $fields = $request->validated();
        // Section code generation logic

        $year = substr(Carbon::now()->year, -2);

        $section_id = 'SC'.'-'.$year.'-'.str_pad(Section::count() + 1, 2, '0', STR_PAD_LEFT);

        $fields['code'] = $section_id;

        $section = Section::create($fields);

        $trackCourses = $section->track->courses;
        $section->courses()->sync($trackCourses);
        // if the request containss a redirectTo parameter it sets the value of $redirectTo with that value but if it doesnt exist the sections.show method is the default
        $redirectTo = $request->input('redirectTo', route('sections.show', $section));

        return redirect($redirectTo)->with('success', 'Track created successfully.');
    }

    public function show(Section $section)
    {
        $section = new SectionResource($section->load(['user', 'program', 'track', 'year', 'semester', 'students', 'courseSectionAssignments.course', 'courseSectionAssignments.instructor']));

        $courses = CourseResource::collection(Course::withExists(['sections as related_to_section' => function ($query) use ($section) {
            return $query->where('sections.id', $section->id);
        }])->orderByDesc('related_to_section')->orderBy('name')->get());

        $instructors = InstructorResource::collection(Instructor::all()->sortBy('name'));

        return Inertia::render('Sections/Show', [
            'section' => $section,
            'courses' => $courses,
            'instructors' => $instructors,
            'studyModes' => $section->studyMode,
            'status' => $section->status,
            'isApproved' => $section->is_approved,
            'isCompleted' => $section->is_completed,
        ]);
    }

    public function update(SectionUpdateRequest $request, Section $section)
    {
        $fields = $request->validated();

        // Update the section record
        $section->update($fields);

        return redirect()->route('sections.show', $section)->with('success', 'Section updated successfully.');
    }

    public function edit(Section $section)
    {
        return Inertia::render('Sections/Edit', [
            'section' => $section->load(['user', 'program', 'track', 'year', 'semester']),
            'programs' => Program::all(),
            'tracks' => Track::all(),
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
