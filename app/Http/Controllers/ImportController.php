<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\StudentsImport;
use App\Imports\UsersImport;
use App\Exports\StudentsExport;
use App\Exports\UsersExport;
use App\Models\User;
use App\Models\Student;
use App\Models\Section;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    // Import students from Excel for a section
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            'section_id' => 'required|exists:sections,id',
        ]);

        Excel::import(new StudentsImport($request->section_id), $request->file('file'));

        return back()->with('success', 'Students imported successfully.');
    }
    
}
