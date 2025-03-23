<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudyModeStoreRequest;
use App\Models\StudyMode;
use Illuminate\Http\Request;

class StudyModeController extends Controller
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
    public function store(StudyModeStoreRequest $request)
    {
        $fields = $request->validated();

        $studyMode = StudyMode::create($fields);

        $redirectTo = request()->query('redirectTo') ?? 'studyModes.index';
        $params = request()->query('params') ?? [];
        return redirect(route($redirectTo, $params));
    }

    /**
     * Display the specified resource.
     */
    public function show(StudyMode $studyMode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudyMode $studyMode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudyMode $studyMode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyMode $studyMode)
    {
        //
    }
}
