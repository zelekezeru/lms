<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\UsersImport;
use App\Exports\StudentsExport;
use App\Exports\UsersExport;
use App\Models\User;
use App\Models\Student;
use App\Models\Section;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;


class ImportController extends Controller
{
    // Import students from Excel for a section
    public function sectionStudents(Request $request)
    {
        $request->validate([
        'section_id' => 'required|exists:sections,id',
        'file' => 'required|file|mimes:xlsx,xls,csv',
    ]);
    
    Excel::import(new StudentsImport($request->section_id), $request->file);

    return back()->with('success', 'Students imported successfully.');
    
    }
}
