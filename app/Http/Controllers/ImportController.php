<?php

namespace App\Http\Controllers;

use App\Imports\StudentsImport;
use App\Models\Section;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
