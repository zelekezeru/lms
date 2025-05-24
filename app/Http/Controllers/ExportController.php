<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport; // Import the StudentsExport class
use App\Models\User;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Optional: To add a header row
use Maatwebsite\Excel\Concerns\WithMapping; // Optional: To customize the data in the export

class ExportController extends Controller
{
    // Export StudentsExcel with a parameter list
    public function exportStudents(Request $request)
    {
        $studentIds = $request->input('student_ids', []);
        
        $students = Student::whereIn('id', $studentIds)->get();

        return Excel::download(new StudentsExport($students), 'students list.xlsx');
    }
}
