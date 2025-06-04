<?php

namespace App\Http\Controllers;

use App\Imports\CenterImport;
use App\Imports\StudentsImport;
use App\Models\Section;
use App\Models\StudyMode;
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

    // Import students from Excel for a center
    public function centerStudents(Request $request)
    {
        $request->validate([
            'center_id' => 'required|exists:centers,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $study_mode_id = StudyMode::where('name', 'DISTANCE')->first()->id; // Assuming you want to use a default study mode

        // Assuming you have a similar import class for center students
        Excel::import(new CenterImport($request->center_id, $study_mode_id), $request->file);

        return back()->with('success', 'Center students imported successfully.');
    }
}
