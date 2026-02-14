<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Student;
use App\Models\Status;
use App\Models\Year;
use Illuminate\Support\Facades\Auth;

class GraduatedStudentsImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // Loop through each row in the Excel file
        foreach ($collection as $row) {
            // Example: assume Excel has columns 'id_number' and 'status'
            $student = Student::where('id_no', $row['id_number'])->first();
            if (!$student) {
                // Optionally log or skip if student not found
                continue;
            }

            $status = $student->status;
            if (!$status) {
                $status = new Status();
                $status->student_id = $student->id;
                $status->user_id = Auth::id();
            }

            // Reset all status fields
            $status->is_active = 0;
            $status->is_verified = 0;
            $status->is_graduated = 0;
            
            // Set status based on imported value
            if ($row['status'] == 'ACTIVE') {
                $status->is_active = 1;
            }
            if ($row['status'] == 'VERIFIED') {
                $status->is_verified = 1;
            }
            if ($row['status'] == 'GRADUATED') {
                $status->is_graduated = 1;
                $status->graduated_by_name = Auth::user()->name;
                $status->graduated_at = $row['year'] ? ($row['year'] . '-06-30') : null;
            }
            $status->save();
        }
    }
}
