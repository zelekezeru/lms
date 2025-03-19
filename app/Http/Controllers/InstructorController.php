<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\Department;
use App\Http\Requests\InstructorRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class InstructorController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Instructors/Index', [
            'instructors' => Instructor::with(['tenant', 'department'])->latest()->paginate(10),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Instructors/Create', [
            'departments' => Department::all(['id', 'name']),
        ]);
    }

    public function show(Instructor $instructor): Response
    {
        return Inertia::render('Instructors/Show', [
            'instructor' => $instructor->load(['tenant', 'user', 'department']),
        ]);
    }


    public function store(InstructorRequest $request)
    {
        $data = $request->validated();

        $data['tenant_id'] = 1; // Hardcoded tenant ID for now

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('instructors', 'public');
        }

        Instructor::create($data);

        return redirect()->route('instructors.index')->with('success', 'Instructor added successfully.');
    }

    public function edit(Instructor $instructor): Response
    {
        return Inertia::render('Instructors/Form', [
            'instructor' => $instructor->load(['tenant', 'user', 'department']),
            'departments' => Department::all(['id', 'name']), // Pass departments
        ]);
    }

    public function update(InstructorRequest $request, Instructor $instructor)
    {
        $data = $request->validated();

        // Handle profile image update
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($instructor->profile_image) {
                Storage::disk('public')->delete($instructor->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('instructors', 'public');
        }

        $instructor->update($data);

        return redirect()->route('instructors.index')->with('success', 'Instructor updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        return redirect()->route('instructors.index')->with('success', 'Instructor deleted successfully.');
    }
}
