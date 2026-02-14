<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    protected $students;

    /**
     * StudentsExport constructor.
     */
    public function __construct($students)
    {
        $this->students = $students;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $i = 1;

        return $this->students->map(function ($student) use (&$i) {
            return [
                'No' => $i++,
                'ID_Number' => $student->id_no,
                'Full_Name' => trim("{$student->first_name} {$student->middle_name} {$student->last_name}"),
                'Sex' => $student->sex,
                'Phone' => $student->mobile_phone,
                'Email_Address' => $student->user->email,
                'Program' => isset($student->program) ? $student->program->name : 'N/A',
                'Study_Mode' => isset($student->studyMode) ? $student->studyMode->name : 'N/A',
                'Address' => $student->address,
                'Status' => isset($student->status) ? ($student->status->is_active ? 'Active' : 'Inactive') : 'Unknown',
                
            ];
        });
    }

    /**
     * Define the column headings for your Excel file.
     */
    public function headings(): array
    {
        return [
            'No',
            'ID_Number',
            'Full_Name',
            'Sex',
            'Phone',
            'Email_Address',
            'Program',
            'Study_Mode',
            'Address',
            'Status',
        ];
    }
}
