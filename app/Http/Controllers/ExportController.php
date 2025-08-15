<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Exports\UsersExport;
use App\Models\Center;
use App\Models\Section; // Import the StudentsExport class
use App\Models\Student;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DistanceReportExport;

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

        $centers = Center::with(['students.grades'])->get();
        return Excel::download(new StudentsExport($centers), 'Center list.xlsx');
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

        return Excel::download(new StudentsExport($students), $center->name.' Students List.xlsx');
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

    public function distanceReport()
    {
        $centers = Center::with(['students.grades'])->get();

        // Check if centers were found.
        if ($centers->isEmpty()) {
            return back()->with('error', 'No data available to export.');
        }
        
        $reportExport = new DistanceReportExport($centers);
        
        return Excel::download($reportExport, 'distance-report.xlsx');
    }
}
