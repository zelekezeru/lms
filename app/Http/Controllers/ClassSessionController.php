<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassSessionStoreRequest;
use App\Models\ClassSession;
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
        $fields = $request->validated();

        $fields['date_time'] = Carbon::parse($fields['date_time'])->toDateTimeString();

        $classSession = ClassSession::create($fields);
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
