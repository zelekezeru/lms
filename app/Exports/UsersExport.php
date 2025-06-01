<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $i = 1;

        return $this->users->map(function ($user) use (&$i) {
            // Check if the user has the 'INSTRUCTOR' role
            if ($user->hasRole('INSTRUCTOR')) {
                $data = [
                    'No' => $i++,
                    'ID_Number' => $user->user_uuid ?? '',
                    'Full Name' => $user->name,
                    'Phone' => $user->phone ?? '',
                    'Email Address' => $user->email,
                    'Specialization' => $user->instructor->specialization ?? '',
                    'EMPLOYMENT TYPE' => $user->instructor->employment_type ?? '',
                    'Status' => $user->instructor->status ?? '',
                ];
            }
            // Check if the user has the 'EMPLOYEE' role
            elseif ($user->hasRole('EMPLOYEE')) {
                $data = [
                    'No' => $i++,
                    'ID_Number' => $user->user_uuid ?? '',
                    'Full Name' => $user->name,
                    'Phone' => $user->phone ?? '',
                    'Email Address' => $user->email,
                    'EMPLOYMENT TYPE' => $user->employee->employment_type ?? '',
                    'Status' => $user->employee->status ?? '',
                ];
            }

            return $data;
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'ID_Number',
            'Full Name',
            'Phone',
            'Email Address',
            'EMPLOYMENT TYPE',
            'Status',
        ];
    }
}
