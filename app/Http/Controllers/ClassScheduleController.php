<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassScheduleStoreRequest;
use App\Models\ClassSchedule;
use App\Models\CourseOffering;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
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
    public function store(ClassScheduleStoreRequest $request)
    {

        $fields = $request->validated();

        $fields['start_time'] = Carbon::parse($fields['start_time'])->format('H:i:s');
        $fields['end_time'] = Carbon::parse($fields['end_time'])->format('H:i:s');

        $courseOffering = CourseOffering::lookUpFor($fields['course_id'], $fields['section_id']);

        if (! $courseOffering) {
            return back()->withErrors('error', 'No Course Offering of such!');
        }

        $fields['course_offering_id'] = $courseOffering->id;
        unset($fields['section_id']);
        unset($fields['course_id']);

        $classSchedule = ClassSchedule::create($fields);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSchedule $classSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassSchedule $classSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassSchedule $classSchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSchedule $classSchedule)
    {
        //
    }
}
