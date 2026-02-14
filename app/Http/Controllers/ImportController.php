<?php

namespace App\Http\Controllers;

use App\Imports\CenterImport;
use App\Imports\StudentsImport;
use App\Imports\GradesImport;
use App\Imports\GraduatedStudentsImport;
use App\Imports\StudentPaymentCodesImport;
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
        
        return inertia('Students/importedStudents', [
            'section' => $section,
            'students' => $newStudents,
            'existingUser' => $duplicateStudents,
        ])->with([
            'import_report' => [
                'registeredCount' => $registeredCount,
                'notRegisteredCount' => $notRegisteredCount,
                'duplicateData' => $duplicateData,
                'duplicateStudents' => $duplicateStudents,
                'success' => "Students imported successfully. ",
            ],
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

        $center = Center::findOrFail($request->input('center_id'));

        // Get only the newly registered student IDs from the import
        $newStudents = $import->getRegisteredStudentIds();
        
        $duplicateStudents = $import->getExistingUserIds();
        
        return inertia('Students/importedStudents', [
            'section' => $center,
            'students' => $newStudents,
            'existingUser' => $duplicateStudents,
        ])->with([
            'import_report' => [
                'registeredCount' => $registeredCount,
                'notRegisteredCount' => $notRegisteredCount,
                'duplicateData' => $duplicateData,
                'duplicateStudents' => $duplicateStudents,
                'success' => "Students imported successfully. ",
            ],
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
            'section_id' => 'sometimes|exists:sections,id',
            'center_id' => 'sometimes|exists:centers,id',
            'course_id' => 'required|exists:courses,id',
        ]);
        
        $sectionId = $request->input('section_id');
        $courseId = $request->input('course_id');
        $centerId = $request->input('center_id');

        $import = new ResultsImport($courseId, $sectionId, $centerId);

        Excel::import($import, $request->file('file'));
        
        // Fetch alert message from import if available
        $alert = method_exists($import, 'getAlert') ? $import->getAlert() : 'Results imported successfully.';
        // If the import has no alert, we can assume it was successful
        if (!$alert) {
            $title = 'Success';
            $alert = 'Results imported successfully.';
        } else {
            // If there was an alert, we can log it or handle it as needed
            $title = 'Alert';
        }

        if ($sectionId) {
            return redirect()->route('assessments.section_course', [
                'section' => $sectionId,
                'course' => $courseId,
            ])->with($title, $alert);
        } elseif ($centerId) {
            return redirect()->route('assessments.center_course', [
                'center' => $centerId,
                'course' => $courseId,
            ])->with($title, $alert);
        }
    }

    // Import Graduated Students Id
    public function importGraduatedStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new GraduatedStudentsImport();
        Excel::import($import, $request->file('file'));

        return back()->with('success', 'Students Status imported successfully.');
    }

    // Import Student Payment Codes
    public function paymentcodes(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        $import = new StudentPaymentCodesImport();
        Excel::import($import, $request->file('file'));

        $report = [
            'updated' => $import->getUpdatedCount(),
            'skipped_missing' => $import->getSkippedMissing(),
            'skipped_duplicate' => $import->getSkippedDuplicate(),
            'not_found' => $import->getNotFound(),
            'conflicts' => $import->getConflicts(),
        ];

        return back()->with([
            'success' => 'Student Payment Codes imported successfully.',
            'import_report' => $report,
        ]);
    }
}
