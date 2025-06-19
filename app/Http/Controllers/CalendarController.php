<?php

namespace App\Http\Controllers;

use App\Http\Resources\SemesterResource;
use App\Http\Services\AutoEnrollmentService;
use App\Models\Semester;
use App\Models\Section;
use App\Http\Resources\SectionResource;
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

        $oldSemesters = $query->where('is_completed', 1)->orderBy('status', 'asc')
            ->orderByDesc('start_date')
            ->get();
        
        $oldSemesters = SemesterResource::collection($oldSemesters->load('sections', 'grades', 'year'));
        
        $activeSemester = Semester::where('status', 'Active')->first();

        $activeSemester = $activeSemester ? new SemesterResource($activeSemester->load('year', 'sections', 'grades')) : null;

        $sections = SectionResource::collection(Section::with(['year', 'students', 'semester', 'program'])->paginate(30));

        // $submittedGrades = $this->submittedGrades();

        // dd($submittedGrades);
        return Inertia::render('Calendars/Index', [
            'oldSemesters' => $oldSemesters,
            'activeSemester' => $activeSemester,
            'search' => $request->search,
            'sections' => $sections,
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

        if (! $activeSemester) {
            return redirect()->back()->with('error', 'No active semester to close.');
        }

        $semesters = SemesterResource::collection(
            Semester::where('status', 'Inactive')->with('year')->orderByDesc('name')->get()
        );

        return Inertia::render('Calendars/CloseForm', [
            'semester' => $activeSemester,
            'semesters' => $semesters,
        ]);
    }

    /**
     * Process the semester closure and start a new one.
     */
    public function closeSemester(Request $request)
    {
        $request->validate([
            'approval' => 'required|accepted',
            'new_semester_id' => 'required|numeric|exists:semesters,id',
            'new_semester_start_date' => 'required|',
            'new_semester_end_date' => 'required|date|after:new_semester_start_date',
        ]);

        $activeSemester = Semester::where('status', 'Active')->first();

        $newSemester = Semester::find($request->new_semester_id);

        if (! $activeSemester || ! $newSemester) {
            return redirect()->back()->with('error', 'No active semester to close.');
        } elseif ($newSemester->status == 'Active') {
            return redirect()->back()->with('error', 'The selected semester is already active.');
        } elseif ($newSemester->id == $activeSemester->id) {
            return redirect()->back()->with('error', 'The selected semester is the same as the current one.');
        }

        DB::transaction(function () use ($request, $activeSemester, $newSemester) {
            // Close current semester
            $activeSemester->update(['status' => 'Inactive']);

            $newSemester->update([
                'status' => 'Active',
                'start_date' => $request->new_semester_start_date,
                'end_date' => $request->new_semester_end_date,
            ]);

            AutoEnrollmentService::autoEnroll();
        });

        return redirect()->route('calendars.index')->with('success', 'Semester closing Instialization successfully Done.');
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
}
