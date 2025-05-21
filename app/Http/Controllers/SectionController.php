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
        $sections = SectionResource::collection(Section::with(['user', 'program', 'track', 'year', 'semester'])->get());

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

        $section_id = 'SC' . '-' . $year . '-' . str_pad(Section::count() + 1, 2, '0', STR_PAD_LEFT);

        $fields['code'] = $section_id;
        $track = Track::find($fields['track_id']);

        $trackCourses = $track->courses()->with(['curricula' => function ($q) use ($fields) {
            return $q->where('track_id', $fields['track_id'])->where('study_mode_id', $fields['study_mode_id']);
        }])->get();

        $trackCoursesOrganized = $trackCourses->mapWithKeys(function ($trackCourse) {
            $curriculum = $trackCourse->curricula->first();
            return [
                $trackCourse->id => [
                    'year_level' => $curriculum->year_level ?? null,
                    'semester' => $curriculum->semester ?? null,
                ]
            ];
        });

        $section = Section::create($fields);

        $section->courses()->sync($trackCoursesOrganized);
        // if the request containss a redirectTo parameter it sets the value of $redirectTo with that value but if it doesnt exist the sections.show method is the default
        $redirectTo = $request->input('redirectTo', route('sections.show', $section));

        return redirect($redirectTo)->with('success', 'Track created successfully.');
    }

    public function show(Section $section)
    {
        $section = new SectionResource($section->load(['user', 'program', 'track', 'year', 'semester', 'students', 'grades', 'courseSectionAssignments.course', 'courseSectionAssignments.instructor']));

        $courses = CourseResource::collection(Course::withExists(['sections as related_to_section' => function ($query) use ($section) {
            return $query->where('sections.id', $section->id);
        }])->orderByDesc('related_to_section')->orderBy('name')->get());

        $currentYearLevel = intval(Year::getCurrentYear()->name) - intval($section->year->name) + 1;
        $currentSemesterLevel = $section->semester->level;
        
        $instructors = InstructorResource::collection(Instructor::with('courses')->get()->sortBy('name'));
        return Inertia::render('Sections/Show', [
            'section' => $section,
            'courses' => $courses,
            'instructors' => $instructors,
            'studyModes' => $section->studyMode,
            'status' => $section->status,
            'isApproved' => $section->is_approved,
            'isCompleted' => $section->is_completed,
            'currentYearLevel' => $currentYearLevel, 
            'currentSemesterLevel' => $currentSemesterLevel, 
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
