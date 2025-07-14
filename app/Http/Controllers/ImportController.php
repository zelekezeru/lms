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

        $import = new StudentsImport($request->section_id);
        Excel::import($import, $request->file('file'));

        // show the imported data
        $registeredCount = $import->getRegisteredCount();
        $notRegisteredCount = $import->getNotRegisteredCount();
        $duplicateData = $import->getDuplicateDataCount();

        $sectionId = $request->input('section_id');
        $section = Section::findOrFail($sectionId);

        // Get only the newly registered student IDs from the import
        $newStudents = $import->getRegisteredStudentIds(); 

        // Pass the import report data to the session for display with the success message
        // if ($registeredCount > 0) {
            return $this->showImportedStudents($request, $sectionId)->with([
            'import_report' => [
                'registeredCount' => $registeredCount,
                'notRegisteredCount' => $notRegisteredCount,
                'duplicateData' => $duplicateData,
                'success' => "Students imported successfully. ",
            ],
            'students' => $newStudents,
            ]);
        // } else {
        //     return back()->with('error', "No students were registered. Please check the file and try again.");
        // }

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

         return back()->with('success', "Center students imported successfully.");
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
    // Show the imported students for a section (GET endpoint)
    public function showImportedStudents(Request $request, $sectionId)
    {
        $section = Section::with('students.user')->findOrFail($sectionId);

        $students = $section->students()->with('user')->get();

        return inertia('Students/importedStudents', [
            'section' => $section,
            'students' => $students,
            'import_report' => null,
        ]);
    }
}
