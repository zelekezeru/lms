<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport; // Import the StudentsExport class
use App\Models\User;
use App\Models\Student;
use App\Models\Section;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Optional: To add a header row
use Maatwebsite\Excel\Concerns\WithMapping; // Optional: To customize the data in the export

class ExportController extends Controller
{
    // Export StudentsExcel with a parameter list
    public function exportSectionstudents($section_id)    
    {
        // Get the student IDs from the section
        $section = Section::find($section_id);

        $students = $section->students()->with('user', 'status', 'church')->orderBy('first_name')->orderBy('middle_name')->get();

        return Excel::download(new StudentsExport($students), 'students list.xlsx');
    }
    // Export UsersExcel with a parameter list
    public function exportUsers($role)
    {
        // Get the users based on the role
        if ($role == 'all') {
            $users = User::all();
        } elseif ($role == 'INSTRUCTOR') {
            $users = User::whereHas('roles', function($query) use ($role) {
                $query->where('name', $role);
            })->with('instructor')->get();
        } elseif ($role == 'EMPLOYEE') {
            $users = User::whereHas('roles', function($query) use ($role) {
                $query->where('name', $role);
            })->with('employee')->get();
        } else {
            $users = collect();
        }
        
        return Excel::download(new UsersExport($users), 'users.xlsx');
    }
}
