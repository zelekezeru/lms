<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgramResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\StudyModeResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\YearResource;
use App\Http\Services\StudentsFilterService;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Program;
use App\Models\Center;
use App\Models\Instructor;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\StudyMode;
use App\Models\Year;

class RegistrarPortalController extends Controller
{
    public function index()
    {
        $user = new UserResource(
            request()->user()
        );

        return inertia('Registrar/Index', [
            'user' => $user,

            // Headline metrics
            'totalStudents' => Student::count(),
            'totalCourses' => Course::count(),
            'availablePrograms' => Program::count(),
            'availableCenters' => Center::count(),
            'totalInstructors' => Instructor::count(),

            // Follow-up metrics (things a registrar acts on)
            'activeStudents' => $this->countStudentsWithStatus('is_active'),
            'graduatedStudents' => $this->countStudentsWithStatus('is_graduated'),
            'scholarshipStudents' => $this->countStudentsWithStatus('is_scholarship'),
            'pendingPaymentStudents' => Student::whereHas('payments', fn($q) => $q->where('status', 'pending'))->count(),
            'unassignedStudents' => Student::whereNull('section_id')->count(),

            // Charts
            'enrollments' => $this->enrollmentProgress(),
            'courseDistribution' => $this->courseDistribution(),
            'studyModeDistribution' => $this->studyModeDistribution(),

            // Activity feed
            'recentActivities' => $this->topNewActivities(),
        ]);
    }

    /**
     * Student directory for the registrar: searchable, filterable, sortable, paginated.
     */
    public function students(Request $request)
    {
        $query = Student::with(['user', 'status', 'program', 'studyMode', 'year']);

        // Free-text search across name / id / phone
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('middle_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('id_no', 'LIKE', "%{$search}%")
                    ->orWhere('mobile_phone', 'LIKE', "%{$search}%");
            });
        }

        // Shared filter logic (payment / status / program / track / study mode / year / section)
        StudentsFilterService::filterStudents($request, $query);

        // Sorting
        $allowedSorts = ['first_name', 'last_name', 'id_no', 'mobile_phone', 'created_at'];
        $sortColumn = $request->input('sortColumn', 'first_name');
        $sortDirection = $request->input('sortDirection', 'asc');

        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $paginatedStudents = $query->paginate(25)->withQueryString();

        return inertia('Registrar/Students/Index', [
            'students' => StudentResource::collection($paginatedStudents),
            'studentsCount' => $paginatedStudents->total(),
            'programs' => ProgramResource::collection(
                Program::with('studyModes', 'tracks.sections.studyMode', 'tracks.sections.year')->orderBy('name')->get()
            ),
            'studyModes' => StudyModeResource::collection(StudyMode::orderBy('name')->get()),
            'years' => YearResource::collection(Year::orderBy('name')->get()),
            'search' => $request->search,
            'sortInfo' => [
                'currentSortColumn' => $sortColumn,
                'currentSortDirection' => $sortDirection,
            ],
        ]);
    }

    /**
     * Aggregated academic reports for the registrar.
     */
    public function reports()
    {
        $totalStudents = Student::count();

        return inertia('Registrar/Reports', [
            'totals' => [
                'students' => $totalStudents,
                'active' => $this->countStudentsWithStatus('is_active'),
                'graduated' => $this->countStudentsWithStatus('is_graduated'),
                'scholarship' => $this->countStudentsWithStatus('is_scholarship'),
                'pendingPayment' => Student::whereHas('payments', fn($q) => $q->where('status', 'pending'))->count(),
                'courses' => Course::count(),
                'programs' => Program::count(),
                'instructors' => Instructor::count(),
                'centers' => Center::count(),
            ],
            'enrollmentTrend' => $this->monthlyEnrollment(11),
            'programDistribution' => $this->programDistribution(),
            'studyModeDistribution' => $this->studyModeDistribution(),
            'genderDistribution' => $this->genderDistribution(),
            'yearDistribution' => $this->yearDistribution(),
            'generatedAt' => now()->toDayDateTimeString(),
        ]);
    }

    /**
     * Count students whose status flag is set.
     */
    private function countStudentsWithStatus(string $flag): int
    {
        return Student::whereHas('status', fn($q) => $q->where($flag, true))->count();
    }

    // Enrollment progress (last 6 months) — used by the dashboard bar chart.
    public function enrollmentProgress()
    {
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        })->reverse();

        $enrollments = Student::where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

        $monthNames = $months->map(function ($month) {
            return \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F');
        });

        $data = $months->mapWithKeys(function ($month) use ($enrollments) {
            return [$month => $enrollments->get($month, 0)];
        });

        return [
            'labels' => $monthNames->values(),
            'data' => $data->values(),
        ];
    }

    /**
     * Monthly new-student counts over the last N+1 months (used by reports trend chart).
     */
    private function monthlyEnrollment(int $monthsBack)
    {
        $months = collect(range(0, $monthsBack))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        })->reverse();

        $enrollments = Student::where('created_at', '>=', now()->subMonths($monthsBack)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

        return [
            'labels' => $months->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values(),
            'data' => $months->map(fn($m) => (int) $enrollments->get($m, 0))->values(),
        ];
    }

    // Course distribution in programs
    public function courseDistribution()
    {
        // Count unique courses assigned to each program (across its tracks).
        $programCourses = Program::with('tracks')->get();

        foreach ($programCourses as $program) {
            $courses = collect();
            foreach ($program->tracks as $track) {
                if (isset($track->courses)) {
                    $courses = $courses->merge($track->courses);
                }
            }
            $program->courses = $courses->unique('id')->values();
        }

        return [
            'labels' => $programCourses->pluck('name'),
            'data' => $programCourses->map(fn($program) => count($program->courses)),
        ];
    }

    /**
     * Number of students per study mode.
     */
    private function studyModeDistribution()
    {
        $studyModes = StudyMode::orderBy('name')->get();

        $counts = Student::selectRaw('study_mode_id, COUNT(*) as count')
            ->groupBy('study_mode_id')
            ->pluck('count', 'study_mode_id');

        return [
            'labels' => $studyModes->pluck('name')->values(),
            'data' => $studyModes->map(fn($sm) => (int) ($counts[$sm->id] ?? 0))->values(),
        ];
    }

    /**
     * Number of students per program.
     */
    private function programDistribution()
    {
        $programs = Program::orderBy('name')->get();

        $counts = Student::selectRaw('program_id, COUNT(*) as count')
            ->groupBy('program_id')
            ->pluck('count', 'program_id');

        return [
            'labels' => $programs->pluck('name')->values(),
            'data' => $programs->map(fn($p) => (int) ($counts[$p->id] ?? 0))->values(),
        ];
    }

    /**
     * Number of students per academic year.
     */
    private function yearDistribution()
    {
        $years = Year::orderBy('name')->get();

        $counts = Student::selectRaw('year_id, COUNT(*) as count')
            ->groupBy('year_id')
            ->pluck('count', 'year_id');

        return [
            'labels' => $years->pluck('name')->values(),
            'data' => $years->map(fn($y) => (int) ($counts[$y->id] ?? 0))->values(),
        ];
    }

    /**
     * Gender split of students.
     */
    private function genderDistribution()
    {
        $counts = Student::selectRaw('sex, COUNT(*) as count')
            ->groupBy('sex')
            ->pluck('count', 'sex');

        $labels = collect($counts->keys())->map(fn($sex) => $sex ?: 'Unspecified');

        return [
            'labels' => $labels->values(),
            'data' => collect($counts->values())->map(fn($c) => (int) $c)->values(),
        ];
    }

    public function topNewActivities()
    {
        // Fetch latest activity from different models
        $student = Student::latest('created_at')->first();
        $instructor = Instructor::latest('created_at')->first();
        $grade = Grade::latest('created_at')->first();
        $semester = Semester::latest('created_at')->first();

        $activities = collect([
            $student ? [
                'type' => 'New Student',
                'name' => $student->first_name . ' ' . $student->last_name ?? 'N/A',
                'created_at' => $student->created_at,
            ] : null,
            $instructor ? [
                'type' => 'Registered new Instructor',
                'name' => $instructor->user->name ?? 'N/A',
                'created_at' => $instructor->created_at,
            ] : null,
            $grade ? [
                'type' => 'Created new grade',
                'name' => $grade->course->name ?? 'N/A',
                'created_at' => $grade->created_at,
            ] : null,
            $semester ? [
                'type' => 'Created new Semester',
                'name' => $semester->name ?? 'N/A',
                'created_at' => $semester->created_at,
            ] : null,
        ])->filter();

        return $activities->sortByDesc('created_at')->take(5)->values();
    }
}
