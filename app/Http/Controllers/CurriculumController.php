<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurriculumStoreRequest;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Semester;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Curriculums/Index', [
            'curricula' => Curriculum::with(['course', 'track', 'semester', 'instructor'])->get(),
            'tracks' => Track::all(),
            'courses' => Course::all(),
            'semesters' => Semester::all(),
            'instructors' => User::where('role', 'instructor')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CurriculumStoreRequest $request)
    {
        $fields = $request->validated();
        $bulkInsert = [];
        $courseIds = $fields['courses'];
        $existingCourseIds = Curriculum::where('track_id', $fields['track_id'])
            ->where('study_mode_id', $fields['study_mode_id'])
            ->where('year_level', $fields['year_level'])
            ->where('semester_level', $fields['semester_level'])
            ->pluck('course_id')
            ->toArray();

        foreach ($existingCourseIds as $index => $existingCourseId) {
            if (! in_array($existingCourseId, $courseIds)) {
                $curriculum = Curriculum::where('track_id', $fields['track_id'])
                    ->where('study_mode_id', $fields['study_mode_id'])
                    ->where('year_level', $fields['year_level'])
                    ->where('semester_level', $fields['semester_level'])
                    ->where('course_id', $existingCourseId)
                    ->delete();

                unset($existingCourseIds[$index]);
            }
        }

        foreach ($courseIds as $courseId) {
            if (! in_array($courseId, $existingCourseIds)) {
                $bulkInsert[] = [
                    'study_mode_id' => $fields['study_mode_id'],
                    'track_id' => $fields['track_id'],
                    'course_id' => $courseId,
                    'year_level' => $fields['year_level'],
                    'semester_level' => $fields['semester_level'],
                    'description' => 'This Course In This Study MOde And Track Should Be Taken' . $fields['description'] ?? 'Year ' . $fields['year_level'] . 'Semester ' . $fields['semester_level'],
                ];
            }
        }

        Curriculum::insert($bulkInsert);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Curriculum $curriculum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Curriculum $curriculum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Curriculum $curriculum)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curriculum $curriculum)
    {
        $curriculum->delete();

        return redirect()->back();
    }
}
