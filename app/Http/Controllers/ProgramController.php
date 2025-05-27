<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramStoreRequest;
use App\Http\Requests\ProgramUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\StudyModeResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\Program;
use App\Models\StudyMode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Program::with('director');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('language', 'like', "%{$search}%")
                    ->orWhereHas('director', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $allowedSorts = ['name', 'language'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;

        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $programs = ProgramResource::collection($query->paginate(15));

        return inertia('Programs/Index', [
            'programs' => $programs, // Corrected to return the programs collection
            'sortInfo' => [
                'currentSortColumn' => $sortColumn,
                'currentSortDirection' => $sortDirection,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        $program = new ProgramResource($program->load('tracks', 'director', 'studyModes'));

        $studyModes = StudyModeResource::collection(StudyMode::all());

        return inertia('Programs/Show', [
            'program' => $program,
            'studyModes' => $studyModes,
            'users' => UserResource::collection(User::all()),
        ]);
    }

    /**a
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = UserResource::collection(User::all());
        $courses = CourseResource::collection(Course::all());

        return inertia('Programs/Create', [
            'users' => $users,
            'courses' => $courses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProgramStoreRequest $request)
    {
        $fields = $request->validated();
        $commonCourses = $fields['courses'] ?? [];
        unset($fields['courses']);

        $year = substr(Carbon::now()->year, -2);

        $program_id = 'PR' . '-' . str_pad(Program::count() + 1, 2, '0', STR_PAD_LEFT) . '-' . $year;

        $fields['code'] = $program_id;

        // Assign the selected director the PROGRAM-DIRECTOR role
        $user = User::where('id', $fields['user_id'])->first();

        $user->assignRole('PROGRAM-DIRECTOR');

        $program = Program::create($fields);

        $syncData = [];

        foreach ($commonCourses as $commonCourse) {
            $syncData[$commonCourse] = ['is_common' => true];
        }

        $program->courses()->sync($syncData);

        // Create A defualt studyMode of mode 'REGULAR'
        $program->studyModes()->attach([
            1 => ['duration' => $program->duration],
        ]);


        return redirect()->route('programs.show', $program)->with('success', 'Program created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $program->load('tracks', 'director', 'courses');
        $courses = CourseResource::collection(Course::withExists(['programs as related_to_program' => function ($query) use ($program) {
            return $query->where('programs.id', $program->id);
        }])->orderByDesc('related_to_program', 'name')->get());

        return inertia('Programs/Edit', [
            'program' => new ProgramResource($program),
            'users' => UserResource::collection(User::all()),
            'courses' => $courses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProgramUpdateRequest $request, Program $program)
    {
        $fields = $request->validated();
        $commonCourseUpdated = $fields['courses'] ?? null;
        unset($fields['courses']);

        $program->update($fields);

        if ($commonCourseUpdated !== null) {
            $syncData = [];
            foreach ($commonCourseUpdated as $courseId) {
                $syncData[$courseId] = ['is_common' => true];
            }
            $program->courses()->sync($syncData);

            if ($program->tracks()->exists()) {
                foreach ($program->tracks as $track) {
                    $existingTrackCourses = $track->courses()
                        ->wherePivot('is_common', false)
                        ->pluck('courses.id')
                        ->toArray();

                    $finalCourses = array_unique(array_merge($existingTrackCourses, $commonCourseUpdated));

                    $syncDataForTrack = [];
                    foreach ($finalCourses as $courseId) {
                        $syncDataForTrack[$courseId] = [
                            'is_common' => in_array($courseId, $commonCourseUpdated),
                        ];
                    }

                    $track->courses()->sync($syncDataForTrack);
                }
            }
        }

        if (isset($fields['user_id'])) {
            $user = User::where('id', $fields['user_id'])->first();
            if ($user && ! $user->hasRole('PROGRAM-DIRECTOR')) {
                $user->assignRole('PROGRAM-DIRECTOR');
            }
        }

        return redirect()->route('programs.show', $program)->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('programs.index')->with('success', 'Program deleted successfully.');
    }
}
