<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeUpdateRequest;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with(['instructor', 'year', 'semester', 'course'])->paginate(30);

        return inertia('Grades/Index', [
            'grades' => $grades,
        ]);
    }

    public function create()
    {
        return inertia('Grades/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|integer|exists:students,id',
            'grades.*.grade_point' => 'required|string',
            'grades.*.grade_letter' => 'required|string',
            'grades.*.grade_description' => 'nullable|string',
            'grades.*.grade_scale' => 'required|string',
            'grades.*.grade_complaint' => 'required|boolean',
            'grades.*.grade_comment' => 'nullable|string',
            'grades.*.changed_grade' => 'nullable',
            'grades.*.grade_status' => 'required|string',
            'grades.*.user_id' => 'required|integer|exists:users,id',
            'grades.*.year_id' => 'required|integer',
            'grades.*.semester_id' => 'required|integer',
            'grades.*.section_id' => 'required|integer',
            'grades.*.course_id' => 'required|integer',
        ]);

        foreach ($data['grades'] as $gradeData) {
            // Update existing or create new based on unique keys (e.g. student, course, section, year, semester)
            Grade::updateOrCreate(
                [
                    'student_id' => $gradeData['student_id'],
                    'course_id' => $gradeData['course_id'],
                    'section_id' => $gradeData['section_id'],
                    'year_id' => $gradeData['year_id'],
                    'semester_id' => $gradeData['semester_id'],

                ],
                $gradeData,

            );

            $student = Student::find($gradeData['student_id']); // Corrected method

            $student->courses()->updateExistingPivot($gradeData['course_id'], [
                'status' => 'Completed',
            ]);
        }

        return redirect()->back()->with('success', 'Grade created successfully.');
    }

    public function show(Grade $grade)
    {
        return inertia('Grades/Show', [
            'grade' => $grade,
        ]);
    }

    public function edit(Grade $grade)
    {
        return inertia('Grades/Edit', [
            'grade' => $grade,
        ]);
    }

    public function update(GradeUpdateRequest $request, Grade $grade)
    {
        $fields = $request->validated();

        $grade->update($fields);

        return redirect()->route('grades.index')->with('success', 'Grade updated successfully.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->route('grades.index')->with('success', 'Grade deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $grades = Grade::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with(['instructor', 'year', 'semester', 'course'])
            ->get();

        return inertia('Grades/SearchResults', [
            'grades' => $grades,
        ]);
    }
}
