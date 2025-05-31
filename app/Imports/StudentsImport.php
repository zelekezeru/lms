<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected $section_id;
    protected $program_id;
    protected $track_id;
    protected $study_mode_id;
    protected $year_id;
    protected $semester_id;
    protected $tenant_id;
    protected $user_id;

    public function __construct($meta = [])
    {
        $this->section_id = $meta['section_id'] ?? null;
        $this->program_id = $meta['program_id'] ?? null;
        $this->track_id = $meta['track_id'] ?? null;
        $this->study_mode_id = $meta['study_mode_id'] ?? null;
        $this->year_id = $meta['year_id'] ?? null;
        $this->semester_id = $meta['semester_id'] ?? null;
        $this->tenant_id = $meta['tenant_id'] ?? null;
        $this->user_id = $meta['user_id'] ?? null;
    }

    public function collection(Collection $rows)
    {
        $section = Section::find($this->section_id);

        dd($section);

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Skip empty rows
                if (!$row['id_number'] || !$row['full_name']) {
                    continue;
                }

                $fullNameParts = explode(' ', $row['full_name']);
                $firstName = $fullNameParts[0] ?? '';
                $middleName = $fullNameParts[1] ?? '';
                $lastName = $fullNameParts[2] ?? '';

                // Create a user if necessary (you can adjust this)
                $user = User::firstOrCreate([
                    'email' => strtolower(Str::slug($firstName . $row['id_number'], '.')) . '@example.com',
                ], [
                    'name' => $firstName . ' ' . $middleName,
                    'password' => Hash::make('password'),
                ]);

                Student::updateOrCreate([
                    'id_no' => $row['id_number'],
                ], [
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'sex' => $row['sex'] ?? 'Male',
                    'mobile_phone' => $row['phone'] ?? '',
                    'address' => $row['address'] ?? '',
                    'date_of_birth' => $row['date_of_birth'] ?? null,
                    'program_id' => $this->program_id,
                    'track_id' => $this->track_id,
                    'study_mode_id' => $this->study_mode_id,
                    'section_id' => $this->section_id,
                    'year_id' => $this->year_id,
                    'semester_id' => $this->semester_id,
                    'tenant_id' => $this->tenant_id,
                    'user_id' => $user->id,
                ]);
            }
        });
    }
}
