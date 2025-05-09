<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // count courses, students, instructors, Revenue
        $courses = count(Course::get());

        $students = count(Student::get());

        $instructors = count(Instructor::get());
        
        $dashboardView = Auth::user()->roles()->first()->name == 'STUDENT' ? 'Student/Dashboard' : 'Dashboard';

        return inertia($dashboardView, [
            'courses' => $courses,
            'students' => $students,
            'instructors' => $instructors,
        ]);
    }
}
