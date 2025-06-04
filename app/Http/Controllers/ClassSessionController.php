<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassSessionStoreRequest;
use App\Models\ClassSession;
use App\Models\CourseOffering;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(ClassSessionStoreRequest $request)
    {
        $validated = $request->validated();

        $start = Carbon::parse($validated['start_date_time']);
        $end = Carbon::parse($validated['end_time']);
        // Replace fields
        $validated['date'] = $start->toDateString();         // YYYY-MM-DD
        $validated['start_time'] = $start->toDateTimeString(); // full datetime
        $validated['end_time'] = $end->toDateTimeString();     // full datetime

        $validated['course_offering_id'] = CourseOffering::lookUpFor($validated['course_id'], $validated['section_id'])->id;

        unset($validated['start_date_time']); // Remove the original datetime input
        unset($validated['course_id']);
        unset($validated['section_id']);

        ClassSession::create($validated);

        return redirect()->back()->with('success', 'Class session created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSession $classSession)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassSession $classSession)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassSession $classSession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSession $classSession)
    {
        //
    }
}
