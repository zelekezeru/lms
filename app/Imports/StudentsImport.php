<?php

namespace App\Imports;

use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Status;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected $section;
    protected $center;
    protected $program_id;
    protected $track_id;
    protected $study_mode_id;
    protected $tenant_id;
    protected $user_id;

    public $registeredCount = 0;
    public $notRegisteredCount = 0;
    public $duplicateData = 0;

    protected $registeredStudentIds = [];

    public function __construct(protected $section_id = null, protected $center_id = null) {}

    public function collection(Collection $rows)
    {
        
        $this->initializeSection();

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                
                if (!$this->hasRequiredFields($row)) {
                    Log::error('Missing Fields: Required fields are missing for row: ' . json_encode($row));
                    continue;
                }
                
                [$firstName, $middleName, $lastName] = $this->parseFullName($row['full_name']);
                if (!$firstName) {
                    Log::error('Full Name Not Found: Full name not found for row: ' . json_encode($row));
                    continue;
                }

                $program = $this->resolveProgram($row['program'] ?? null);

                if (!$program) {
                    $this->notRegisteredCount++;
                    Log::error('Program Not Found: Program not found for code: ' . ($row['program'] ?? 'N/A'));
                    continue;
                }

                $phone = $this->formatPhone($row['phone'] ?? '');
                
                $email = $this->generateEmail($firstName, $middleName);

                $existingUser = User::where('email', $email)->orWhere('phone', $phone)->first();

                if ($existingUser) {
                    $this->duplicateData++;
                    Log::error('Duplicate User: User with email ' . $email . ' or phone ' . $phone . ' already exists.');
                    continue;
                }
                
                [$year, $semester, $academicYear] = $this->getOrCreateYearAndSemester($row['entry_year']);

                $userUuid = $this->generateUserUuid($academicYear);

                $defaultPassword = $this->generateDefaultPassword($firstName, $phone);
                
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'user_uuid' => $userUuid,
                        'name' => "$firstName $middleName",
                        'password' => Hash::make($defaultPassword),
                        'default_password' => $defaultPassword,
                    ]
                );
                
                $student = Student::updateOrCreate(
                    ['user_id' => $user->id],
                    $this->prepareStudentData($row, $firstName, $middleName, $lastName, $userUuid, $year, $semester, $user->id)
                );

                $user->assignRole('STUDENT');

                $this->createStudentStatus($student);

                if (!empty($row['pastor_name']) || !empty($row['church_name'])) {
                    $this->createStudentChurch($student, $row->toArray());
                }

                $this->registeredCount++;
                $this->registeredStudentIds[] = $student->id;
            }
        });
    }

    protected function initializeSection()
    {
        $this->section = Section::with(['user', 'program', 'track', 'studyMode'])->findOrFail($this->section_id);

        $this->program_id = $this->section->program->id;
        $this->track_id = $this->section->track->id;
        $this->study_mode_id = $this->section->studyMode->id;
        $this->tenant_id = Auth::user()->tenant_id;
        $this->user_id = $this->section->user->id;
    }

    protected function hasRequiredFields($row)
    {
        return isset($row['full_name'], $row['program'], $row['entry_year']);
    }

    protected function resolveProgram($code): bool
    {
        if (!$code) {
            return false;
        }

        $program = Program::where('code', $code)->first();

        if (!$program) {
            $program = Program::where('name', 'like', "%$code%")->first();
        }
        
        if ($program) {
            $this->program_id = $program->id;
            $this->track_id = $program->tracks()->first()?->id;
            $this->study_mode_id = $program->studyModes()->first()?->id ?? 1;

            return true;
        } else {
            $this->program_id = $this->program_id;
            $this->track_id = $this->track_id;
            return false;
        }
    }

    private function parseFullName($fullName)
    {
        $parts = preg_split('/\s+/', trim($fullName));
        if (count($parts) === 3) {
            return [
            ucfirst(strtolower($parts[0])),
            ucfirst(strtolower($parts[1])),
            ucfirst(strtolower($parts[2]))
            ];
        } elseif (count($parts) === 2) {
            return [
            ucfirst(strtolower($parts[0])),
            ucfirst(strtolower($parts[1])),
            ''
            ];
        } else {
            // Log invalid full name format
            Log::warning('Invalid Full Name: Name must be First Middle Last.');
            return [null, null, null];
        }
    }

    protected function generateEmail($firstName, $middleName)
    {
        return strtolower(Str::slug($firstName) . '.' . Str::slug($middleName)) . '@sits.edu.et';
    }

    protected function generateUserUuid($academicYear)
    {
        $academicYear = substr($academicYear, -2);
        $count = str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
        return "SITS-R-{$count}-{$academicYear}";
    }

    protected function generateDefaultPassword($firstName, $phone)
    {        
        $last4 = substr($phone, -4);
        return strtolower($firstName) . '@' . $last4;
    }

    protected function getOrCreateYearAndSemester($entryYear)
    {
        $year = Year::where('name', $entryYear)->first();
        
        if (!$year) {
            $year = Year::create(['name' => $entryYear]);
        }
        
        $semester = Semester::firstOrCreate(
            ['year_id' => $year->id],
            [
                'name' => '1st - ' . $year->name,
                'level' => 1,
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
            ]
        );

        return [$year, $semester, $year->name];
    }

    protected function prepareStudentData($row, $firstName, $middleName, $lastName, $userUuid, $year, $semester, $userId)
    {
        return [
            'id_no' => $userUuid,
            'old_id' => $row['old_id'] ?? null,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'sex' => isset($row['sex']) ? strtoupper($row['sex']) : '',
            'mobile_phone' => $this->formatPhone($phone ?? ''),
            'address' => $row['address'] ?? null,
            'date_of_birth' => $this->parseDateOfBirth($row['date_of_birth'] ?? null),
            'program_id' => $this->program_id,
            'track_id' => $this->track_id,
            'study_mode_id' => $this->study_mode_id,
            'section_id' => $this->section_id,
            'year_id' => $year->id,
            'semester_id' => $semester->id,
            'tenant_id' => $this->tenant_id,
            'user_id' => $userId,
        ];
    }

    protected function parseDateOfBirth($date)
    {
        if (!$date) return null;
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    protected function formatPhone($phone)
    {
        // Normalize student phone number
        $phone = trim($phone);

        if (isset($phone) && str_starts_with($phone, '9')) {
            $phone = '+251 ' . $phone;
        } elseif (isset($phone) && str_starts_with($phone, '7')) {
            $phone = '+251 ' . $phone;
        } elseif (isset($phone) && !str_starts_with($phone, '0')) {
            $phone = '+251 ' . $phone;
        } elseif (!isset($phone) || $phone === '') {
            $phone = '+251 900000000';
        }

        return $phone;
    }

    protected function createStudentStatus(Student $student)
    {
        Status::create([
            'student_id' => $student->id,
            'user_id' => $student->user->id,
            'is_active' => false,
            'created_by_name' => Auth::user()?->name ?? 'Excel Import',
            'created_at' => now(),
        ]);
    }

    protected function createStudentChurch(Student $student, array $fields)
    {
        $pastorPhone = $fields['pastor_phone'] ?? null;
        if ($pastorPhone && (str_starts_with($pastorPhone, '9'))) {
            $pastorPhone = '+251 ' . $pastorPhone;
        } elseif ($pastorPhone && (str_starts_with($pastorPhone, '7'))) {
            $pastorPhone = '+254 ' . $pastorPhone;
        } elseif ($pastorPhone && !str_starts_with($pastorPhone, '0')) {
            $pastorPhone = '+251 ' . $pastorPhone;
        }

        $student->church()->create([
            'pastor_name' => $fields['pastor_name'] ?? null,
            'pastor_phone' => $pastorPhone,
            'position_denomination' => $fields['position_denomination'] ?? null,
            'church_name' => $fields['church_name'] ?? null,
            'church_address' => $fields['church_address'] ?? null,
        ]);
    }

    public function getRegisteredCount()
    {
        return $this->registeredCount;
    }

    public function getNotRegisteredCount()
    {
        return $this->notRegisteredCount;
    }

    public function getDuplicateDataCount()
    {
        return $this->duplicateData;
    }

    public function getRegisteredStudentIds()
    {
        return Student::whereIn('id', $this->registeredStudentIds)->get();
    }
}
