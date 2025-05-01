<?php

namespace App\Http\Controllers;

use App\Models\cr;
use App\Models\Course;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Http\Request;

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

        return inertia('Dashboard', [
            'courses' => $courses,
            'students' => $students,
            'instructors' => $instructors,
        ]);
    }
}
