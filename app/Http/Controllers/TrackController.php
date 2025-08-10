<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackStoreRequest;
use App\Http\Requests\TrackUpdateRequest;
use App\Http\Resources\CenterResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CurriculumResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\TrackResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\YearResource;
use App\Models\Center;
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

        $tracks = $query->withCount(['students', 'curricula'])->paginate(30);
        
        return inertia('Tracks/Index', [
            'tracks' => $tracks,
            'programs' => ProgramResource::collection(Program::all()),
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
                        'course_id' => $commonCourse,
                    ]);
                }
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
        $track = new TrackResource($track->load(
            'program.studyModes.semesters.year',
            'courses',
            'sections.semester',
            'sections.year',
            'sections.studyMode',
            'curricula.course',
            'curricula.studyMode',
        ));

        $years = YearResource::collection(Year::all());

        $centers = CenterResource::collection(Center::all());
        
        return Inertia::render('Tracks/Show', [
            'track'    => $track,
            'centers'  => CenterResource::collection(Center::all()),
            'years'    => YearResource::collection(Year::all()),
            'tracks'   => TrackResource::collection(Track::all()),

            'courses' => Inertia::defer(
                fn() =>
                CourseResource::collection(
                    Course::withExists(['tracks as related_to_track' => fn($q) => $q->where('tracks.id', $track->id)])
                        ->orderBy('name')
                        ->get()
                )
            ),

            'students' => Inertia::defer(
                fn() =>
                StudentResource::collection(
                    $track->students()->with(['studyMode', 'section'])->orderBy('first_name', 'asc')->orderBy('middle_name', 'asc')->paginate(50)
                )
            ),
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
        // Check if the track has associated sections or students
        if ($track->sections()->count() > 0) {
            return redirect()->route('tracks.index')->with('error', 'Cannot delete track with associated sections.');
        } elseif ($track->students()->count() > 0) {
            return redirect()->route('tracks.index')->with('error', 'Cannot delete track with associated students.');
        } elseif ($track->curricula()->count() > 0) {
            return redirect()->route('tracks.index')->with('error', 'Cannot delete track with associated curricula.');
        } elseif ($track->courses()->count() > 0) {
            return redirect()->route('tracks.index')->with('error', 'Cannot delete track with associated courses.');
        } elseif ($track->programs()->count() > 0) {
            return redirect()->route('tracks.index')->with('error', 'Cannot delete track with associated programs.');
        } elseif ($track->courseOfferings()->count() > 0) {
            return redirect()->route('tracks.index')->with('error', 'Cannot delete track with associated course offerings.');
        } else {
            $track->delete();

            return redirect(route('tracks.index'))->with('success', 'Track deleted successfully.');
        }
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $tracks = Track::where('track_name', 'like', "%$search%")
            ->orWhere('track_id', 'like', "%$search%")
            ->latest()
            ->paginate(50);

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

    /**
     * Copy curricula from one track to another for a given study mode.
     */
    public function copyCurriculaFromTrack(Request $request)
    {
        $request->validate([
            'from_track' => 'required|exists:tracks,id',
            'to_track' => 'required|exists:tracks,id',
            'study_mode_id' => 'required|exists:study_modes,id',
        ]);
        
        $fromTrackId = $request->input('from_track');
        $toTrackId = $request->input('to_track');
        $studyModeId = $request->input('study_mode_id');

        // Get all curricula from the source track for the selected study mode
        $fromCurricula = Curriculum::where('track_id', $fromTrackId)
            ->where('study_mode_id', 1)
            ->get();

        // Remove existing curricula for the target track and study mode
        Curriculum::where('track_id', $toTrackId)
            ->where('study_mode_id', $studyModeId)
            ->delete();

        // Group curricula by year_level and semester_level for batch insert
        $grouped = $fromCurricula->groupBy(function ($item) {
            return $item->year_level . '-' . $item->semester_level;
        });
        
        foreach ($grouped as $key => $curriculaGroup) {
            $fields = [
                'study_mode_id' => $studyModeId,
                'track_id' => $toTrackId,
                'year_level' => $curriculaGroup->first()->year_level,
                'semester_level' => $curriculaGroup->first()->semester_level,
                'description' => $curriculaGroup->first()->description,
            ];
            $courseIds = $curriculaGroup->pluck('course_id')->toArray();

            $existingCourseIds = Curriculum::where('track_id', $fields['track_id'])
                ->where('study_mode_id', $fields['study_mode_id'])
                ->where('year_level', $fields['year_level'])
                ->where('semester_level', $fields['semester_level'])
                ->pluck('course_id')
                ->toArray();

            foreach ($existingCourseIds as $index => $existingCourseId) {
                if (! in_array($existingCourseId, $courseIds)) {
                    Curriculum::where('track_id', $fields['track_id'])
                        ->where('study_mode_id', $fields['study_mode_id'])
                        ->where('year_level', $fields['year_level'])
                        ->where('semester_level', $fields['semester_level'])
                        ->where('course_id', $existingCourseId)
                        ->delete();
                    unset($existingCourseIds[$index]);
                }
            }

            $bulkInsert = [];
            foreach ($courseIds as $courseId) {
                if (! in_array($courseId, $existingCourseIds)) {
                    $bulkInsert[] = [
                        'study_mode_id' => $fields['study_mode_id'],
                        'track_id' => $fields['track_id'],
                        'course_id' => $courseId,
                        'year_level' => $fields['year_level'],
                        'semester_level' => $fields['semester_level'],
                        'description' => 'This Course In This Study MOde And Track Should Be Taken ' . ($fields['description'] ?? 'Year ' . $fields['year_level'] . ' Semester ' . $fields['semester_level']),
                    ];
                }
            }

            if (!empty($bulkInsert)) {
                Curriculum::insert($bulkInsert);
            }
        }

        return back()->with('success', 'Curriculum copied successfully.');
    }
}
