<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Track;
use App\Models\Course;
use App\Models\Semester;
use App\Models\User;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd(request()->all());
        return Inertia::render('Curriculums/Index', [
            'curricula' => Curriculum::with(['course', 'track', 'semester', 'instructor'])->get(),
            'tracks' => Track::all(),
            'courses' => Course::all(),
            'semesters' => Semester::all(),
            'instructors' => User::where('role', 'instructor')->get()
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
    public function store(Request $request)
    {
        $request->validate([
            'track_id' => 'required',
            'course_id' => 'required',
            'semester_id' => 'required',
            // additional fields...
        ]);

        Curriculum::create($request->all());

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
