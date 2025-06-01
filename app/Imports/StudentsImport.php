<?php

namespace App\Imports;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Models\Status;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Add this import if SectionResource exists in this namespace

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

    public function __construct($section_id)
    {
        $this->section_id = $section_id;
    }

    public function collection(Collection $rows)
    {
        $section = Section::findOrFail($this->section_id);

        $section = new SectionResource($section->load([
            'user', 'program', 'track', 'year', 'semester', 'studyMode',
        ]));

        $this->program_id = $section->program->id;
        $this->track_id = $section->track->id;
        $this->study_mode_id = $section->studyMode->id;
        $this->year_id = $section->year->id;
        $this->semester_id = $section->semester->id;
        $this->tenant_id = $section->user->tenant_id; // assuming user relation carries tenant
        $this->user_id = $section->user->id;

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                if (! $row['id_number'] || ! $row['full_name']) {
                    continue;
                }

                $fullNameParts = explode(' ', $row['full_name']);
                $firstName = $fullNameParts[0] ?? '';
                $middleName = $fullNameParts[1] ?? '';
                $lastName = $fullNameParts[2] ?? '';

                $email = strtolower(Str::slug($firstName).'.'.Str::slug($middleName)).'@sits.edu.et';

                // 👤 Generate custom user_uuid
                $studentCount = str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);

                // This year last two digits
                $academicYear = substr(date('Y'), -2); // Get last two digits of the current year

                $userUuid = 'SITS-'.str_pad($studentCount, 4, '0', STR_PAD_LEFT).'-'.$academicYear;

                $default_password = strtolower($firstName).'@'.substr($row['phone'], -4); // Default password for new users

                $user = User::firstOrCreate(['email' => $email], [
                    'user_uuid' => $userUuid,
                    'name' => $firstName.' '.$middleName,
                    'email' => $email,
                    'password' => Hash::make($default_password),
                    'default_password' => $default_password,
                ]);

                // 👨‍🎓 Create Student
                $student = Student::updateOrCreate([
                    'id_no' => $userUuid,
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

                // ✅ Register Student Status
                $this->createStudentStatus($student);

                // ⛪️ Add Church Info (if data exists)
                if (! empty($row['pastor_name']) || ! empty($row['church_name'])) {
                    $this->createStudentChurch($student, $row->toArray());
                }
            }
        });
    }

    private function createStudentStatus(Student $student): void
    {
        $status = new Status;
        $status->student_id = $student->id;
        $status->user_id = $student->user->id;
        $status->is_active = true;
        $status->created_by_name = Auth::user()?->name ?? 'System';
        $status->created_at = now();
        $status->save();
    }

    private function createStudentChurch(Student $student, array $fields): void
    {
        $church_data = [
            'student_id' => $student->id,
            'pastor_name' => $fields['pastor_name'] ?? null,
            'pastor_phone' => $fields['pastor_phone'] ?? null,
            'position_denomination' => $fields['position_denomination'] ?? null,
            'church_name' => $fields['church_name'] ?? null,
            'church_address' => $fields['church_address'] ?? null,
        ];

        $student->church()->create($church_data);
    }
}
