<?php

namespace App\Http\Controllers;

use App\Imports\CenterImport;
use App\Imports\StudentsImport;
use App\Imports\GradesImport;
use App\Imports\ResultsImport;
use App\Imports\s;
use App\Models\Section;
use App\Models\Center;
use App\Models\StudyMode;
use App\Models\Student;
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

        $lastStudentId = Student::max('id') ?? 0;

        $import = new StudentsImport($request->section_id, $center_id = null, $request);

        Excel::import($import, $request->file('file'));
        // show the imported data
        $registeredCount = $import->getRegisteredCount();
        $notRegisteredCount = $import->getNotRegisteredCount();
        $duplicateData = $import->getDuplicateDataCount();

        $section = Section::findOrFail($request->input('section_id'));

        // Get only the newly registered student IDs from the import
        $newStudents = $import->getRegisteredStudentIds();
        $duplicateStudents = $import->getExistingUserIds();

        return $this->showImportedStudents($request, $section->id, $duplicateStudents)->with([
            'import_report' => [
                'registeredCount' => $registeredCount,
                'notRegisteredCount' => $notRegisteredCount,
                'duplicateData' => $duplicateData,
                'duplicateStudents' => $duplicateStudents,
                'success' => "Students imported successfully. ",
            ],
            'students' => $newStudents,
        ]);
    }

    // Import students from Excel for a center
    public function centerStudents(Request $request)
    {
        $request->validate([
            'center_id' => 'required|exists:centers,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        // Last student ID is not used in this method, but kept for consistency
        $lastStudentId = Student::max('id') ?? 0;

        // Assuming CenterImport exposes these properties after import
        $import = new CenterImport($request->center_id);
        Excel::import($import, $request->file('file'));

        // show the imported data
        $registeredCount = $import->getRegisteredCount();
        $notRegisteredCount = $import->getNotRegisteredCount();
        $duplicateData = $import->getDuplicateDataCount();

        $centerId = $request->input('center_id');
        $center = Center::findOrFail($centerId)->load('students.user');
        
        // Get only the newly registered student IDs from the import
        $newStudents = $import->getRegisteredStudentIds();
        $duplicateStudents = $import->getExistingUserIds();

        $newStudents = Student::whereIn('id', $newStudents)->with('user')->get();

        return $this->showImportedStudents($request, $centerId, $duplicateStudents)->with([
            'import_report' => [
                'registeredCount' => $registeredCount,
                'notRegisteredCount' => $notRegisteredCount,
                'duplicateData' => $duplicateData,
                'success' => "Students imported successfully. ",
            ],
            'students' => $newStudents,
        ]);
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

    // Import results for a section
    public function importSectionResults(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'section_id' => 'required|exists:sections,id',
            'course_id' => 'required|exists:courses,id',
        ]);
        $sectionId = $request->input('section_id');
        $courseId = $request->input('course_id');

        $import = new ResultsImport($courseId, $sectionId);

        Excel::import($import, $request->file('file'));

        return back()->with('success', 'Results imported successfully.');
    }

    // Show the imported students for a section (GET endpoint)
    public function showImportedStudents(Request $request, $sectionId, $existingUser = [])
    {
        $section = Section::with('students.user')->findOrFail($sectionId);

        $students = $section->students()->with('user')->get();

        return inertia('Students/importedStudents', [
            'section' => $section,
            'students' => $students,
            'existingUser' => $existingUser,
            'import_report' => [null
            ],
        ]);
    }
}
