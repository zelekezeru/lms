<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Exports\UsersExport;
use App\Models\Center;
use App\Models\Section; // Import the StudentsExport class
use App\Models\Student;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

// Optional: To add a header row
// Optional: To customize the data in the export

class ExportController extends Controller
{
    // Export Section StudentsExcel with a parameter list
    public function exportSectionstudents($section_id)
    {
        // Get the student IDs from the section
        $section = Section::find($section_id);

        $students = $section->students()->with('user', 'status', 'church')->orderBy('first_name')->orderBy('middle_name')->get();

        return Excel::download(new StudentsExport($students), $section->name . 'students list.xlsx');
    }
    

    // Export Center StudentsExcel with a parameter list
    public function exportCenterStudents($center_id)
    {
        $center = Center::findOrFail($center_id);

        $students = $center->students()
            ->with('user', 'status', 'church')
            ->orderBy('first_name')
            ->orderBy('middle_name')
            ->get();
        
        return Excel::download(new StudentsExport($students), $center->name . 'students.xlsx');
    }

    // Export UsersExcel with a parameter list
    public function exportUsers($role)
    {
        // Get the users based on the role
        if ($role == 'all') {
            $users = User::all();
        } elseif ($role == 'INSTRUCTOR') {
            $users = User::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })->with('instructor')->get();
        } elseif ($role == 'EMPLOYEE') {
            $users = User::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })->with('employee')->get();
        } else {
            $users = collect();
        }

        return Excel::download(new UsersExport($users), 'users.xlsx');
    }
}
