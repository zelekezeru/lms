<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index()
    {
        // Fetch students from the database
        $students = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'enrolled' => '2021-09-01'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'enrolled' => '2021-09-01'],
            // Add more student data as needed
        ];

        return Inertia::render('Students/IndexStudents', ['students' => $students]);
    }

    public function create()
    {
        return Inertia::render('Students/Create');
    }

    public function show($id)
    {
        // Fetch student by id from the database
        $student = ['id' => $id, 'name' => 'John Doe', 'email' => 'john@example.com', 'enrolled' => '2021-09-01'];

        return Inertia::render('Students/ShowStudents', ['student' => $student]);
    }

    public function edit($id)
    {
        // Fetch student by id from the database
        $student = ['id' => $id, 'name' => 'John Doe', 'email' => 'john@example.com', 'enrolled' => '2021-09-01'];

        return Inertia::render('Students/EditStudents', ['student' => $student]);
    }

    public function destroy($id)
    {
        // Add logic to delete the student from the database

        return redirect()->route('students.index');
    }
}
