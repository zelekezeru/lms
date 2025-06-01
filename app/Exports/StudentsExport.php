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
                'Full Name' => trim("{$student->first_name} {$student->middle_name} {$student->last_name}"),
                'Sex' => $student->sex,
                'Phone' => $student->mobile_phone,
                'Email Address' => $student->user->email,
                'Program' => isset($student->program) ? $student->program->name : 'N/A',
                'Track' => isset($student->track) ? $student->track->name : 'N/A',
                'Study Mode' => isset($student->studyMode) ? $student->studyMode->name : 'N/A',
                'Section' => isset($student->section) ? $student->section->name : 'N/A',
                'Address' => $student->address,
                'Date of Birth' => $student->date_of_birth,
                'Status' => isset($student->status) ? ($student->status->is_active ? 'Active' : 'Inactive') : 'Unknown',
                'Enrollment Status' => isset($student->status) ? ($student->status->is_enrolled ? 'Enrolled' : 'Not Enrolled') : 'Unknown',
                'Graduation Status' => isset($student->status) ? ($student->status->is_graduated ? 'Graduated' : 'Not Graduated') : 'Unknown',

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
            'Full Name',
            'Sex',
            'Phone',
            'Email Address',
            'Program',
            'Track',
            'Study Mode',
            'Section',
            'Address',
            'Date of Birth',
            'Status',
            'Enrollment Status',
            'Graduation Status',
        ];
    }
}
