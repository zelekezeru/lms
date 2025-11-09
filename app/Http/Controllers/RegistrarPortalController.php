<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Program;
use App\Models\Center;
use App\Models\Instructor;
use App\Models\Grade;
use App\Models\Semester;

class RegistrarPortalController extends Controller
{
    public function index()
    {
        $user = new UserResource(
            request()->user()
        );

        $totalStudents = count(Student::all());
        
        $totalCourses = count(Course::all());

        $enrollments = $this->enrollmentProgress();

        $courseDistribution = $this->courseDistribution();

        $availablePrograms = count(Program::all());

        $availableCenters = count(Center::all());

        $recentActivities = $this->topNewActivities();
        
        return inertia('Registrar/Index', [
            'user' => $user,
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'enrollments' => $enrollments,
            'courseDistribution' => $courseDistribution,
            'availablePrograms' => $availablePrograms,
            'availableCenters' => $availableCenters,
            'recentActivities' => $recentActivities,
        ]);
    }

    // Enrollment progress
    public function enrollmentProgress()
    {
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        })->reverse();

        $enrollments = Student::where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

        // Convert months to month names only (e.g., "June")
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

    // Course distribution in programs
    public function courseDistribution()
    {
        // Assuming Course has a 'program' relationship or 'program_id' field
        // Count unique courses assigned to each program track (no duplicates)
        $programCourses = Program::with('tracks')->get();

        foreach ($programCourses as $program) {
            // Assuming each program has many tracks, and each track has many courses
            $courses = collect();
            foreach ($program->tracks as $track) {
                if (isset($track->courses)) {
                    $courses = $courses->merge($track->courses);
                }
            }
            // Remove duplicate courses by id
            $program->courses = $courses->unique('id')->values();
        }
        $labels = $programCourses->pluck('name');
        $data = $programCourses->map(function ($program) {
            return count($program->courses);
        });
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function topNewActivities()
    {
        // Fetch latest 3 activities from different models
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

        // Sort all by created_at desc and take top 3
        $topActivities = $activities->sortByDesc('created_at')->take(5)->values();

        return $topActivities;
    }
}
