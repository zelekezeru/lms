<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $courses = Course::count();
        $students = Student::count();
        $instructors = Instructor::count();

        // Example: Group students by month (fake logic; adapt to your DB schema)
        $studentEnrollments = Student::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Fill missing months with 0 (from Jan to Dec)
        $monthlyData = [];
        foreach (range(1, 12) as $month) {
            $monthlyData[] = $studentEnrollments[$month] ?? 0;
        }

        return inertia('Dashboard', [
            'courses' => $courses,
            'students' => $students,
            'instructors' => $instructors,
            'studentEnrollments' => $monthlyData,
            'userDistribution' => [
                'students' => $students,
                'instructors' => $instructors,
                'courses' => $courses,
            ],
        ]);
    }
}
