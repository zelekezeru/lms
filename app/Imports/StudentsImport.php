<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Student([
            'id_no' => $row['id_no'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'] ?? null,
            'mobile_phone' => $row['mobile_phone'],
            'office_phone' => $row['office_phone'] ?? null,
            'date_of_birth' => $row['date_of_birth'] ?? null,
            'marital_status' => $row['marital_status'] ?? null,
            'sex' => $row['sex'],
            'address' => $row['address'] ?? null,
            'student_signature' => $row['student_signature'] ?? null,
            'office_use_notes' => $row['office_use_notes'] ?? null,
            
            'program_id' => $row['program_id'],
            'track_id' => $row['track_id'],
            'study_mode_id' => $row['study_mode_id'] ?? null,
            'section_id' => $row['section_id'] ?? null,
            'year_id' => $row['year_id'],
            'semester_id' => $row['semester_id'],
            
        ]);
    }
}
