<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionStoreRequest;
use App\Http\Requests\SectionUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\YearResource;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Room;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Track;
use App\Models\User;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $programs = ProgramResource::collection(Program::with('tracks', 'studyModes')->get());

        $years = YearResource::collection(Year::with('semesters')->get()->sortBy('name'));

        $users = UserResource::collection(User::all()->sortBy('name'));

        return Inertia::render('Sections/Create', [
            'programs' => $programs,
            'years' => $years,
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

        DB::beginTransaction();
        try {
            $section = Section::create($fields);

            foreach ($trackCourses as $trackCourse) {
                $curriculum = $trackCourse->curricula->first();

                CourseOffering::updateOrCreate(
                    [
                        'course_id' => $trackCourse->id,
                        'section_id' => $section->id,
                    ],
                    [
                        'year_level' => $curriculum->year_level ?? null,
                        'semester_level' => $curriculum->semester ?? null,
                    ],
                );
            }
            DB::commit();
        } catch (\Exception $th) {
            DB::rollBack();

            return back()->withErrors('errors', 'Error occured');
        }

        // if the request containss a redirectTo parameter it sets the value of $redirectTo with that value but if it doesnt exist the sections.show method is the default
        $redirectTo = $request->input('redirectTo', route('sections.show', $section));

        return redirect($redirectTo)->with('success', 'Track created successfully.');
    }

    public function show(Section $section)
    {
        $section = new SectionResource($section->load([
            'user',
            'program',
            'track',
            'year',
            'semester',
            'studyMode',
            'students',
            'grades',
            'courseOfferings.course',
            'courseOfferings.instructor',
            'classSchedules.courseOffering',
            'classSchedules.semester',
            'classSchedules.room',
        ]));

        $courses = CourseResource::collection(Course::withExists(['courseOfferings as related_to_course_offering' => function ($query) use ($section) {
            return $query->where('section_id', $section->id);
        }])->orderBy('name')->orderByDesc('related_to_course_offering')->get());

        $currentSemester = Semester::getActiveSemester();
        $currentYearLevel = intval(Year::getCurrentYear()->name) - intval($section->year->name) + 1;
        $currentSemesterLevel = $currentSemester->level;
        $instructors = InstructorResource::collection(Instructor::with('courses', 'user')->get()->sortBy('name'));

        $rooms = RoomResource::collection(Room::orderBy('capacity')->get());

        return Inertia::render('Sections/Show', [
            'section' => $section,
            'courses' => $courses,
            'instructors' => $instructors,
            'studyModes' => $section->studyMode,
            'rooms' => $rooms,
            'status' => $section->status,
            'isApproved' => $section->is_approved,
            'isCompleted' => $section->is_completed,
            'currentYearLevel' => $currentYearLevel,
            'currentSemesterLevel' => $currentSemesterLevel,
            'currentSemester' => $currentSemester,
        ]);
    }

    public function update(SectionUpdateRequest $request, Section $section)
    {
        $fields = $request->validated();

        $track = Track::find($fields['track_id']);

        $trackCourses = $track->courses()->with(['curricula' => function ($q) use ($fields) {
            $q->where('track_id', $fields['track_id'])
                ->where('study_mode_id', $fields['study_mode_id']);
        }])->get();

        DB::beginTransaction();

        try {
            // Update the section fields
            $section->update($fields);

            foreach ($trackCourses as $trackCourse) {
                $curriculum = $trackCourse->curricula->first();

                CourseOffering::updateOrCreate(
                    [
                        'course_id' => $trackCourse->id,
                        'section_id' => $section->id,
                    ],
                    [
                        'year_level' => $curriculum->year_level ?? null,
                        'semester_level' => $curriculum->semester ?? null,
                    ]
                );
            }

            DB::commit();

            // Optional redirect logic
            $redirectTo = $request->input('redirectTo', route('sections.show', $section));

            return redirect($redirectTo)->with('success', 'Section updated successfully.');
        } catch (\Exception $th) {
            DB::rollBack();

            return back()->withErrors(['error' => 'An error occurred: ' . $th->getMessage()]);
        }
    }


    public function edit(Section $section)
    {
        $section = new SectionResource($section->load('program', 'track', 'studyMode', 'year', 'semester', 'user'));
        $programs = ProgramResource::collection(Program::with('tracks', 'studyModes')->get());

        $years = YearResource::collection(Year::with('semesters')->get()->sortBy('name'));

        $users = UserResource::collection(User::all()->sortBy('name'));

        return Inertia::render('Sections/Edit', [
            'section' => $section,
            'programs' => $programs,
            'years' => $years,
            'users' => $users,
        ]);
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->back()->with('success', 'Section deleted successfully.');
    }
}
