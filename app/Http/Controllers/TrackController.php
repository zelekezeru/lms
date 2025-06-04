<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackStoreRequest;
use App\Http\Requests\TrackUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CurriculumResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\TrackResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\YearResource;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Curriculum;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Track;
use App\Models\User; // Ensure this class exists in the specified namespace
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TrackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Track::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%");
        }

        $allowedSorts = ['name', 'code', 'description'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;
        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $tracks = $query->paginate(15)->withQueryString();

        return inertia('Tracks/Index', [
            'tracks' => TrackResource::collection($tracks),
            'programs' => ProgramResource::collection(Program::all()),
            'users' => UserResource::collection(User::all()),
            'search' => $request->search,
            'sortInfo' => [
                'currentSortColumn' => $sortColumn,
                'currentSortDirection' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = UserResource::collection(User::all());

        $programs = ProgramResource::collection(Program::all());

        return inertia('Tracks/Create', [
            'users' => $users,
            'programs' => $programs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrackStoreRequest $request)
    {
        $fields = $request->validated();
        $year = substr(Carbon::now()->year, -2);

        $track_id = 'DP' . '/' . str_pad(Track::count() + 1, 3, '0', STR_PAD_LEFT) . '/' . $year;

        $fields['code'] = $track_id;
        DB::beginTransaction();
        try {
            $track = Track::create($fields);
            $studyModes = $track->program->studyModes->pluck('id');

            $year = substr(Carbon::now()->year, -2);
            // Create Section-1 by default for each study modes the program that the created track belongs to has.
            $sections = $studyModes->map(function ($studyModeId) use ($fields, $track, $year) {
                return Section::create([
                    'name' => 'Section-1',
                    'code' => 'SC' . '-' . $year . '-' . str_pad(Section::count() + 1, 2, '0', STR_PAD_LEFT),
                    'program_id' => $fields['program_id'],
                    'track_id' => $track->id,
                    'study_mode_id' => $studyModeId,
                    'year_id' => Year::where('status', 'active')->first()->id,
                    'semester_id' => Semester::where('status', 'active')->first()->id,
                ]);
            });

            $commonCourses = $track->program->courses()->wherePivot('is_common', true)->pluck('courses.id');

            $track->courses()->syncWithoutDetaching($commonCourses);

            foreach ($sections as $section) {
                foreach ($commonCourses as $commonCourse) {
                    CourseOffering::updateOrCreate([
                        'section_id' => $section->id,
                        'course_id' => $commonCourse
                    ]);
                };
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('error', 'something went wrong');
        }
        // if the request containss a redirectTo parameter it sets the value of $redirectTo with that value but if it doesnt exist the tracks.show method is the default
        $redirectTo = $request->input('redirectTo', route('tracks.show', $track));

        return redirect($redirectTo)->with('success', 'Track created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Track $track)
    {
        $track = new TrackResource($track->load('program', 'program.studyModes', 'courses', 'sections', 'sections.semester', 'sections.year', 'sections.studyMode', 'curricula', 'curricula.course', 'curricula.studyMode', 'students', 'students.section'));

        $courses = CourseResource::collection(
            Course::withExists(['tracks as related_to_track' => function ($query) use ($track) {
                $query->where('tracks.id', $track->id);
            }])
                ->orderBy('name')
                ->get()
        );

        $years = YearResource::collection(Year::with('semesters')->get());

        $curriculums = CurriculumResource::collection(Curriculum::all());

        return inertia('Tracks/Show', [
            'track' => $track,
            'courses' => $courses,
            'years' => $years,
            'curriculums' => $curriculums,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Track $track)
    {
        $track->load('program');

        return inertia('Tracks/Edit', [
            'track' => new TrackResource($track),
            'users' => UserResource::collection(User::all()),
            'programs' => ProgramResource::collection(Program::all()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrackUpdateRequest $request, Track $track)
    {
        $fields = $request->validated();

        // Optionally regenerate the track code if needed
        if (! $track->code) {
            $year = substr(Carbon::now()->year, -2);
            $track_id = 'DP' . '/' . str_pad(Track::count(), 3, '0', STR_PAD_LEFT) . '/' . $year;
            $fields['code'] = $track_id;
        }

        $track->update($fields);

        return redirect(route('tracks.show', $track))->with('success', 'Track updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Track $track)
    {
        $track->delete();

        return redirect(route('tracks.index'))->with('success', 'Track deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $tracks = Track::where('track_name', 'like', "%$search%")
            ->orWhere('track_id', 'like', "%$search%")
            ->latest()
            ->paginate(15);

        return Inertia::render('Tracks/Index', compact('tracks'));
    }

    public function attach(Request $request, Track $track)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $track->curriculums()->sync($validated['courses']); // sync to update the curricula list

        return redirect()->back()->with('success', 'Courses assigned to curricula successfully.');
    }
}
