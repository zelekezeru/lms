<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Curriculum;
use Inertia\Inertia;

class CurriculumCourseController extends Controller
{
    public function edit(Curriculum $curriculum)
    {
        $courses = Course::all();
        return Inertia::render('Curriculum/AssignCourses', [
            'curriculum' => $curriculum->load('courses'),
            'courses' => $courses,
        ]);
    }

    public function update(Request $request, Curriculum $curriculum)
    {
        $request->validate([
            'courses' => 'required|array',
            'courses.*.id' => 'required|exists:courses,id',
            'courses.*.year' => 'required|numeric',
            'courses.*.semester' => 'required|numeric',
        ]);

        $syncData = [];

        foreach ($request->courses as $course) {
            $syncData[$course['id']] = [
                'year' => $course['year'],
                'semester' => $course['semester'],
                'course_type' => $course['course_type'] ?? null,
            ];
        }

        $curriculum->courses()->sync($syncData);

        return redirect()->route('curricula.index')->with('success', 'Courses assigned.');
    }
}
