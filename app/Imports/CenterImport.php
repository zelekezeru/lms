<?php

namespace App\Imports;

use App\Http\Resources\CenterResource;
use App\Models\Center;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Status;
use App\Models\Student;
use App\Models\StudyMode;
use App\Models\User;
use App\Models\Year;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Carbon;

class CenterImport implements ToCollection, WithHeadingRow
{
    protected $center_id;
    protected $tenant_id;
    protected $study_mode_id;

    protected $duplicate_entries = [];

    public function __construct($center_id)
    {
        $this->center_id = $center_id;
    }

    public function collection(Collection $rows)
    {
        // Load the center and coordinator with user
        $center = Center::with('coordinator.user')->findOrFail($this->center_id);

        // Determine tenant and study mode
        $studyMode = StudyMode::where('name', 'DISTANCE')->firstOrFail();

        $this->study_mode_id = $studyMode->id;

        $this->tenant_id = optional(optional($center->coordinator)->user)->tenant_id ?? Auth::user()->tenant_id;

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // 1. Program & track & section
                $program = $this->resolveProgram($row['program'] ?? null);
                if (!$program) continue;

                $track = $program->tracks()->first();
                if (!$track) {
                    $this->flashError('Track Not Found', "No track found for program {$program->code}");
                    continue;
                }

                $section = $track->sections()->first();
                if (!$section) {
                    $this->flashError('Section Not Found', "No section found for track {$track->name}");
                    continue;
                }

                // 2. Validate row fields
                if (!$this->hasRequiredFields($row, ['full_name', 'phone', 'sex'])) {
                    $this->duplicate_entries[] = $row['full_name'] ?? '[Unknown Name]';
                    continue;
                }

                // 3. Parse full name
                $nameParts = $this->parseFullName($row['full_name']);
                if (!$nameParts) continue;
                [$firstName, $middleName, $lastName] = $nameParts;

                // 4. Email & UUID
                $email = $this->generateEmail($firstName, $middleName);
                if (User::where('email', $email)->exists()) {
                    $this->flashError('Duplicate User', "User with email $email already exists.");
                    continue;
                }

                [$year, $semester, $academicYear] = $this->getOrCreateYearAndSemester($row['entry_year'] ?? null);

                $userUuid = $this->generateUserUuid('D', $academicYear);

                // 5. User creation
                $defaultPassword = $this->generateDefaultPassword($firstName, $row['phone'] ?? '');
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'user_uuid' => $userUuid,
                        'name' => "$firstName $middleName",
                        'password' => Hash::make($defaultPassword),
                        'default_password' => $defaultPassword,
                    ]
                );

                // 6. Student creation
                $studentData = [
                    'id_no' => $userUuid,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'sex' => strtoupper($row['sex']),
                    'mobile_phone' => $this->formatPhone($row['phone']),
                    'address' => $row['address'] ?? null,
                    'date_of_birth' => $this->parseDate($row['date_of_birth'] ?? null),
                    'program_id' => $program->id,
                    'track_id' => $track->id,
                    'section_id' => $section->id,
                    'study_mode_id' => $this->study_mode_id,
                    'year_id' => $year->id,
                    'semester_id' => $semester->id,
                    'tenant_id' => $this->tenant_id,
                    'user_id' => $user->id,
                ];

                $student = Student::updateOrCreate(['user_id' => $user->id], $studentData);

                $user->assignRole('STUDENT');

                // 7. Attach center if not attached
                $student->centers()->syncWithoutDetaching([$this->center_id]);

                // 8. Status
                $this->createStudentStatus($student);

                // 9. Church info
                if (!empty($row['pastor_name']) || !empty($row['church_name'])) {
                    $this->createStudentChurch($student, $row->toArray(), $row['center_distance'] ?? null);
                }
            }
        });
    }

    /** ---------------------------------------------
     * ✅  Helper methods below
     * --------------------------------------------- */

    private function resolveProgram($code)
    {
        if (!$code) {
            $this->flashError('Program Missing', 'Program field is required.');
            return null;
        }

        $program = Program::where('code', strtoupper($code))->first();
        if (!$program) {
            $this->flashError('Program Not Found', "Program {$code} does not exist.");
            return null;
        }

        return $program;
    }

    private function parseFullName($fullName)
    {
        $parts = preg_split('/\s+/', trim($fullName));
        if (count($parts) === 3) {
            return [$parts[0], $parts[1], $parts[2]];
        } elseif (count($parts) === 2) {
            return [$parts[0], $parts[1], 'User'];
        } else {
            $this->flashError('Invalid Full Name', 'Name must be First Middle Last.');
            return null;
        }
    }

    private function generateEmail($first, $middle)
    {
        return strtolower(Str::slug($first) . '.' . Str::slug($middle)) . '@sits.edu.et';
    }

    private function getOrCreateYearAndSemester($entryYear)
    {
        if ($entryYear) {
            $year = Year::where('name', $entryYear)->first();
        }

        if (empty($year)) {
            $year = Year::where('status', 'Active')->first();
        }

        if (!$year) {
            $year = Year::create(['name' => $entryYear ?? now()->year, 'status' => 'Active']);
        }

        $semester = Semester::where('year_id', $year->id)->first();
        if (!$semester) {
            $semester = Semester::create([
                'name' => '1st - ' . $year->name,
                'year_id' => $year->id,
                'level' => 1,
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
            ]);
        }

        $academicYear = substr($year->name, -2);

        return [$year, $semester, $academicYear];
    }

    private function generateUserUuid($type, $academicYear)
    {
        $count = str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
        return "SITS-{$type}-{$count}-{$academicYear}";
    }

    private function generateDefaultPassword($firstName, $phone)
    {
        $last4 = $phone ? substr($phone, -4) : '0000';
        return strtolower($firstName) . '@' . $last4;
    }

    private function formatPhone($phone)
    {
        if (!$phone) return '0900000000';
        return str_starts_with($phone, '9') ? '0' . $phone : $phone;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private function createStudentStatus(Student $student): void
    {
        $status = new Status();
        $status->student_id = $student->id;
        $status->user_id = $student->user->id;
        $status->is_active = true;
        $status->created_by_name = Auth::user()?->name ?? 'System';
        $status->save();
    }

    private function createStudentChurch(Student $student, array $fields, $fallbackAddress = null): void
    {
        $pastorPhone = $fields['pastor_phone'] ?? null;
        if ($pastorPhone && str_starts_with($pastorPhone, '9')) {
            $pastorPhone = '0' . $pastorPhone;
        }

        $churchAddress = $fields['church_address'] ?? $fallbackAddress;

        $student->church()->create([
            'student_id' => $student->id,
            'pastor_name' => $fields['pastor_name'] ?? null,
            'pastor_phone' => $pastorPhone,
            'position_denomination' => $fields['position_denomination'] ?? null,
            'church_name' => $fields['church_name'] ?? null,
            'church_address' => $churchAddress,
        ]);
    }

    private function flashError($title, $text)
    {
        session()->flash('sweet_alert', [
            'type' => 'error',
            'title' => $title,
            'text' => $text,
        ]);
    }

    private function hasRequiredFields($row, $fields)
    {
        foreach ($fields as $field) {
            if (!isset($row[$field]) || empty($row[$field])) {
                $this->flashError('Missing Fields', "Field '$field' is required.");
                return false;
            }
        }
        return true;
    }
}
