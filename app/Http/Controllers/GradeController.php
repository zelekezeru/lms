<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeUpdateRequest;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Weight;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            'grades.*.grade_complaint' => 'nullable|boolean',
            'grades.*.grade_comment' => 'nullable|string',
            'grades.*.changed_grade' => 'nullable',
            'grades.*.grade_status' => 'required|string',
            'grades.*.user_id' => 'required|integer|exists:users,id',
            'grades.*.year_id' => 'required|integer',
            'grades.*.semester_id' => 'required|integer',
            'grades.*.section_id' => 'required|integer',
            'grades.*.course_id' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            foreach ($data['grades'] as $gradeData) {
                $weights = Weight::where('course_id', $gradeData['course_id'])
                    ->where('section_id', $gradeData['section_id'])
                    ->get();

                $sum = $weights->sum(fn($w) => (int) $w->point);

                if ($sum !== 100) {
                    throw new \Exception('The sum of the weights must be 100.');
                }

                // Set the 'completed' column of the section's course offering to 1
                DB::table('course_offerings')
                    ->where('course_id', $gradeData['course_id'])
                    ->where('section_id', $gradeData['section_id'])
                    ->update(['completed' => 1]);

                Grade::updateOrCreate(
                    [
                        'student_id' => $gradeData['student_id'],
                        'course_id' => $gradeData['course_id'],
                        'section_id' => $gradeData['section_id'],
                        'year_id' => $gradeData['year_id'],
                        'semester_id' => $gradeData['semester_id'],
                    ],
                    $gradeData
                );

                $student = Student::findOrFail($gradeData['student_id']);

                $enrollment = $student->enrollments()
                    ->whereHas(
                        'courseOffering',
                        fn($q) => $q->where('course_id', $gradeData['course_id'])
                    )
                    ->first();

                if ($enrollment) {
                    $enrollment->update([
                        'status' => 'completed',
                        'academic_status' => $gradeData['grade_letter'] === 'F' ? 'failed' : 'completed',
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Grades saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['weights' => $e->getMessage()]);
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

        $data = [
            'changed_by' => Auth::user()->id,
            'changed_grade' => $fields['changed_grade'] ?? null,
            'grade_comment' => $fields['grade_comment'] ?? null,
            'grade_letter' => $fields['changed_letter'] ?? $grade->grade_letter,
            'grade_point' => $fields['changed_grade'] ?? $grade->grade_point,
        ];
        
        $grade->update($data);

        return redirect()->back()->with('success', 'Grade updated successfully.');
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

    // Store Student Grade
    public function storeStudentGrade(Request $request, Student $student)
    {
        $fields = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'grade_letter' => 'required|string|max:2',
            'grade_point' => 'required|numeric|min:0|max:100',
                ]);

        $courseOffering = CourseOffering::where('course_id', $fields['course_id'])
                                        ->where('section_id', $student->section_id)
                                        ->with('course', 'section.studyMode.semesters', 'instructor')
                                        ->get()->keyBy(function ($item) {
                                            // Create a unique key for lookup if needed, or just get the first one
                                            return $item->section_id;
                                        })->first();
        $year = Year::where('name', $student->year->name + $courseOffering->year_level - 1)->first();

        $semester = $year->semesters->where('level', $courseOffering->semester_level)->first();

        $enrollment = $student->enrollments()
            ->where('course_offering_id', $courseOffering->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$enrollment) {
            $enrollment = Enrollment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'course_offering_id' => $courseOffering->id,
                    'semester_id' => $semester->id,
                ],
                [
                    'status' => 'enrolled',
                    'academic_status' => 'in_progress',
                ]
            );
        }
        
        $data = [
            'student_id' => $student->id,
            'course_id' => $fields['course_id'],
            'grade_letter' => $fields['grade_letter'],
            'grade_point' => $fields['grade_point'],
            'grade_description' => $fields['grade_description'] ?? null,
            'grade_scale' => 100,
            'grade_status' => $fields['grade_status'] ?? 'completed',
            'user_id' => Auth::id(),
            'semester_id' => $semester->id ?? null,
            'year_id' => $semester->year_id ?? null,
            'section_id' => $student->section_id ?? null,

        ];

        try {
            DB::beginTransaction();

            Grade::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id' => $fields['course_id'],
                    'section_id' => $student->section_id,
                ],
                $data
            );

            $academicStatus = ($fields['grade_letter'] === 'F') ? 'failed' : 'completed';

            $enrollment->update([
                'academic_status' => $academicStatus,
            ]);

            DB::commit();

            return back()->with('success', 'Grade created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['weights' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Grade created successfully.');
    }
}