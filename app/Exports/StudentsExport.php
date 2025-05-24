<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    protected $students;

    /**
     * StudentsExport constructor.
     * @param $students
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
        return $this->students->map(function ($student) {
            return [
                'ID_Number' => $student->id_no,
                'Full Name' => trim("{$student->first_name} {$student->middle_name} {$student->last_name}"),
                'Sex' => $student->sex,
                'Phone' => $student->mobile_phone,
                'Email Address' => $student->user->email,
                'Address' => $student->address,
                'Date of Birth' => $student->date_of_birth,
            ];
        });
    }

    /**
     * Define the column headings for your Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID_Number',
            'Full Name',
            'Sex',
            'Phone',
            'Email Address',
            'Address',
            'Date of Birth',
        ];
    }
}
