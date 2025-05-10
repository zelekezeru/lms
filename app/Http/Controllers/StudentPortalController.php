<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;

use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function index()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return inertia('StudentPortal/Dashboard', [
            'student' => $student,
        ]);
    }
}
