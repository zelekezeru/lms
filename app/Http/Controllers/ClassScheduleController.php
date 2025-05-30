<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassScheduleStoreRequest;
use App\Models\ClassSchedule;
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

        $fields['start_date'] = Carbon::parse($fields['start_date'])->format('Y-m-d');
        $fields['end_date'] = Carbon::parse($fields['end_date'])->format('Y-m-d');
        $fields['start_time'] = Carbon::parse($fields['start_time'])->format('H:i:s');
        $fields['end_time'] = Carbon::parse($fields['end_time'])->format('H:i:s');

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
