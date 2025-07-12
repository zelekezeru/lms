<?php

namespace App\Http\Controllers;

use App\Imports\CenterImport;
use App\Imports\StudentsImport;
use App\Imports\GradesImport;
use App\Models\Section;
use App\Models\StudyMode;
use Illuminate\Http\Request;
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

        $imported = Excel::import(new StudentsImport($request->section_id), $request->file);

        return back()->with('success', "Students imported successfully.");

    }

    // Import students from Excel for a center
    public function centerStudents(Request $request)
    {
        $request->validate([
            'center_id' => 'required|exists:centers,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        
        // Assuming CenterImport exposes these properties after import
        $import = new CenterImport($request->center_id);
        Excel::import($import, $request->file);

        $registeredCount = $registeredCount;
        $notRegisteredCount = $notRegisteredCount;
        $duplicateData = $duplicateData;
        $message = "Import completed: {$registeredCount} registered, {$notRegisteredCount} not registered, {$duplicateData} duplicates.";
        return back()->with('success', "Center students imported successfully. {$message}");
    }

     public function gradesImport(Request $request)
        {
            $request->validate([
            'grades_file' => 'required|mimes:xlsx,xls,csv,ods,tsv,txt',
            ]);
            
            $context = [
            'course_id'   => $request->course_id,
            'section_id'  => $request->section_id,
            'semester_id' => $request->semester_id,
            'year_id'     => $request->year_id,
            'user_id'     => Auth::id(),
        ];
        
        Excel::import(new GradesImport($context), $request->file('grades_file'));

        return back()->with('success', 'Grades imported successfully.');
    }
}
