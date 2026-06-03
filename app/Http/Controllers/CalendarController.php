<?php

namespace App\Http\Controllers;

use App\Http\Resources\SemesterResource;
use App\Http\Resources\CourseResource;
use App\Http\Services\AutoEnrollmentService;
use App\Http\Resources\SectionResource;
use App\Http\Resources\StudyModeResource;
use App\Models\Semester;
use App\Models\Section;
use App\Models\Course;
use App\Models\StudyMode;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CalendarController extends Controller
{
    /**
     * Display a listing of the semesters with optional search.
     */
    public function index(Request $request)
    {
        $query = Semester::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('status', 'LIKE', "%{$search}%");
        }
        
        $studyModes = StudyModeResource::collection(StudyMode::with(['semesters' => fn($q) => $q->wherePivot('status', 'inactive')->orderBy('created_at'), 'semesters.year'])->get());

        $activeSemester = null;
        foreach ($studyModes as $studyMode) {
            $semester = $studyMode->activeSemester();
            if ($semester) {
                $activeSemester = $semester;
                break;
            }
        }
        $noActiveSemesterFound = is_null($activeSemester);

        $sections = Section::with(['students', 'semester'])->get();

        $gradesPercentages = null;

        $gradesPercentages = [];
        if (! $noActiveSemesterFound) {
            foreach ($sections as $section) {
                $gradesPercentages[] = $this->sectionSemesterGradesPercentage($activeSemester, $section);
            }
        }
        
        return Inertia::render('Calendars/Index', [
            'studyModes' => $studyModes,
            // 'oldSemesters' => $oldSemesters,
            // 'activeSemester' => $activeSemester,
            'search' => $request->search,
            'sections' => $sections,
            'gradesPercentages' => $gradesPercentages,
        ]);
    }

    public function submittedGrades()
    {
        $sections = Section::withCount(['courses as submitted_courses_count' => function ($query) {
            $query->where('grades_submitted', true);
        }, 'courses as total_courses_count'])
            ->get();

        $results = $sections->map(function ($section) {
            $total = $section->total_courses_count;
            $submitted = $section->submitted_courses_count;
            $percentage = $total > 0 ? round(($submitted / $total) * 100, 2) : 0;
            return [
                'section_id' => $section->id,
                'section_name' => $section->name,
                'submitted_courses' => $submitted,
                'total_courses' => $total,
                'percentage_submitted' => $percentage,
            ];
        });

        return response()->json($results);
    }

    /**
     * Display the currently active semester.
     */
    public function showActive()
    {
        $activeSemester = Semester::where('status', 'Active')->first();

        if (! $activeSemester) {
            return redirect()->back()->with('error', 'No active semester found.');
        }

        return Inertia::render('Calendars/ShowActive', [
            'semester' => $activeSemester,
        ]);
    }

    /**
     * Show the semester closing form.
     */
    public function closeSemesterForm()
    {
        $activeSemester = Semester::where('status', 'Active')->with('year')->first();
        $studyModes = StudyModeResource::collection(StudyMode::with('semesters.year')->get());

        if (! $activeSemester) {
            return redirect()->back()->with('error', 'No active semester to close.');
        }

        $semesters = SemesterResource::collection(
            Semester::where('status', 'Inactive')->with('year')->orderByDesc('name')->get()
        );


        return Inertia::render('Calendars/CloseForm', [
            'semester' => $activeSemester,
            'studyModes' => $studyModes,
            'semesters' => $semesters,
        ]);
    }

    /**
     * Process the semester closure and start a new one.
     */
    public function closeSemester(Request $request)
    {
        $validated = $request->validate([
            'approval' => 'required|accepted',
            'new_semester_id' => 'required|numeric|exists:semesters,id',
            'study_mode_id' => 'required|numeric|exists:study_modes,id',
        ]);

        $selectedStudyMode = StudyMode::find($validated['study_mode_id']);
        if (! $selectedStudyMode->semesters()->where('semesters.id', $validated['new_semester_id'])->exists()) {
            return redirect()->back()->withErrors(['common' => 'the provided semester is not not applied to this semester!']);
        }

        $activeSemester = $selectedStudyMode->activeSemester();

        $isAlreadyActive = $activeSemester && $activeSemester->id == $validated['new_semester_id'];
        $isClosed = $selectedStudyMode->semesters()->where('semesters.id', $validated['new_semester_id'])->wherePivot('status', 'closed')->exists();

        if ($isAlreadyActive) {
            return redirect()->back()->withErrors(['new_semester_id' => 'The semester is already active!']);
        } elseif ($isClosed) {
            return redirect()->back()->withErrors(['new_semester_id' => 'The semester is closed!']);
        }

        // Dispatch background job to handle active status sync and student enrollment asynchronously
        \App\Jobs\CloseSemesterJob::dispatch($validated['study_mode_id'], $validated['new_semester_id'], Auth::id());

        return redirect()->route('calendars.index')->with('success', 'Semester closing and auto-enrollment has been initialized in the background.');
    }

    /**
     * Show semester detail.
     */
    public function show(Semester $semester)
    {
        $semester->created_at_formatted = $semester->created_at->format('F j, Y');
        $semester->updated_at_formatted = $semester->updated_at->format('F j, Y');

        return Inertia::render('Calendars/Show', [
            'semester' => $semester,
        ]);
    }

    /**
     * Calculate the percentage of courses in a section for a given semester whose grades are "Submitted".
     *
     * @param \App\Models\Semester $semester
     * @param \App\Models\Section $section
     * @return array
     */
    public function sectionSemesterGradesPercentage(Semester $activeSemester, Section $section)
    {
        $currentSemesterLevel = $activeSemester->level;

        $courses = $section->courseOfferings->filter(function ($courseOffering) use ($activeSemester, $section) {
            return $courseOffering->year_level == $section->yearLevel() && $courseOffering->semester_level == $activeSemester->level;
        });

        $totalCourses = $courses->count();

        $submittedCourses = $courses->where('completed', 1)->count();

        $percentage = $totalCourses > 0 ? round(($submittedCourses / $totalCourses) * 100, 2) : 0;

        $section_students = count($section->students);
        return [
            'section_id' => $section->id,
            'semester_id' => $activeSemester->id,
            'total_courses' => $totalCourses,
            'submitted_courses' => $submittedCourses,
            'percentage_submitted' => $percentage,
            'courses' => CourseResource::collection($courses->values()),
            'section' => $section,
            'section_students' => $section_students
        ];
    }
}
