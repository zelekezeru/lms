<?php

namespace App\Imports;

use App\Http\Resources\CenterResource;
use App\Models\Center;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Status;
use App\Models\Student;
use App\Models\StudyMode;
use App\Models\Section;
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
    protected $program_id;
    protected $track_id;

    protected $duplicate_entries = [];
    protected $registeredCount = 0;
    protected $notRegisteredCount = 0;
    protected $duplicateData = 0;
    protected $registeredStudentIds = [];
    protected $existingUserIds = [];

    public function __construct($center_id)
    {
        $this->center_id = $center_id;
    }

    public function collection(Collection $rows)
    {
        // Load the center and coordinator with user
        $center = Center::with('coordinator.user')->findOrFail($this->center_id);

        $this->tenant_id = Auth::user()->tenant_id;

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Determine tenant and study mode

                // 1. Program & track & section
                if (!$this->hasRequiredFields($row, ['full_name', 'phone', 'sex', 'program'])) {
                    $this->notRegisteredCount++;
                    continue;
                }

                [$firstName, $middleName, $lastName] = $this->parseFullName($row['full_name']);
                if (!$firstName) {
                    $this->notRegisteredCount++;
                    continue;
                }

                $program = $this->resolveProgram($row['program'] ?? null, $row['study_mode'] ?? null);
                
                if (!is_object($program)) {
                    $this->notRegisteredCount++;
                    $this->flashError('Program Not Found', "Program not found for code: " . ($row['program'] ?? 'N/A'));
                    continue;
                }

                $track = $program->tracks()->first();
                if (!$track) {
                    $this->flashError('Track Not Found', "No track found for program {$program->code}");
                    continue;
                }
                // 2. Validate row fields
                if (!$this->hasRequiredFields($row, ['full_name', 'phone', 'sex'])) {
                    $this->notRegisteredCount++;
                    continue;
                }

                // 3. Parse full name
                $nameParts = $this->parseFullName($row['full_name']);
                if (!$nameParts) continue;
                [$firstName, $middleName, $lastName] = $nameParts;

                // 4. Email & UUID
                $email = $this->generateEmail($firstName, $middleName);

                $phone = $this->formatPhone($row['phone'] ?? '');

                $existingUser = User::where('email', $email)->where('phone', $phone)->first();

                if ($existingUser) {
                    $this->duplicateData++;
                    $this->existingUserIds[] = $existingUser->id;
                    continue;
                }

                [$year, $semester, $academicYear] = $this->getOrCreateYearAndSemester($row['entry_year'] ?? null);

                                $section = $this->sectionAssigned($track, $year);

                $userUuid = $this->generateUserUuid($academicYear, $this->study_mode_id ?? 1);

                // 5. User creation
                $defaultPassword = $this->generateDefaultPassword($firstName, $row['phone'] ?? '');
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'user_uuid' => $userUuid,
                        'name' => "$firstName $middleName",
                        'phone' => $phone,
                        'tenant_id' => $this->tenant_id,
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
                    'sex' => isset($row['sex']) ? strtoupper($row['sex']) : '',
                    'mobile_phone' => $phone,
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

                $this->registeredCount++;
                $this->registeredStudentIds[] = $student->id;

                $user->assignRole('STUDENT');

                // 8. Status
                $this->createStudentStatus($student);

                // 7. Attach center if not attached
                $student->centers()->syncWithoutDetaching([$this->center_id]);

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

    protected function resolveProgram($code, $studyModeName)
    {
        if (!$code) {
            return null;
        }

        $code = strtoupper(trim($code));
        if (str_contains($code, '-')) {
            $code = str_replace('-', '/', $code);
        }

        $program = Program::where('code', $code)->first();

        if (!$program) {
            $program = Program::where('name', 'like', "%$code%")->first();
        }

        if($studyModeName != null)
        {
            $studyMode = StudyMode::where('name', $studyModeName)->first();

            if ($studyMode) {
                $this->study_mode_id = $studyMode->id;
            }
        }else{
            $studyMode = StudyMode::where('name', 'DISTANCE')->first();
            $program?->studyModes()->syncWithoutDetaching($studyMode->id);
            $this->study_mode_id = $studyMode->id;
        }
        
        if ($program) {
            $this->program_id = $program->id;
            $this->track_id = $program->tracks()->first()?->id;
            $this->study_mode_id = $program->studyModes()->first()?->id;

            return $program;
        } else {
            $this->program_id = $this->program_id;
            $this->track_id = $this->track_id;
            return null;
        }
    }

    private function sectionAssigned($track, $year)
    {
        if ($track->sections()->where('year_id', $year->id)->exists()) {
            $section = $track->sections()->where('year_id', $year->id)->first();
        } else {

            $section = Section::updateOrCreate([
                    'name' => $year->name . '-' . $track->name . ' Section-1',
                'code' => 'SC-' . substr($year->name, -2) . '-' . str_pad(Section::where('year_id', $year->id)->count() + 1, 2, '0', STR_PAD_LEFT),
                'program_id' => $track->program_id,
                'track_id' => $track->id,
                    'study_mode_id' => $this->study_mode_id,
                    'year_id' => $year->id,
                    'semester_id' => Semester::where('year_id', $year->id)->first()->id,
                    'center_id' => $this->center_id ?? null,
                ]);
            $track->sections()->save($section);
        }

        return $section;
    }

    private function generateEmail($first, $middle)
    {
        return strtolower(Str::slug($first) . '.' . Str::slug($middle)) . '@sits.edu.et';
    }

    private function getOrCreateYearAndSemester($entryYear)
    {
        if (Year::where('name', $entryYear)->exists()) {

            $year = Year::where('name', $entryYear)->first();

            if ($year->semesters()->exists()) {
                $semester = $year->semesters()->first();
            } else {
                $semester = Semester::create([
                    'name' => '1st - ' . $year->name,
                    'year_id' => $year->id,
                    'level' => 1,
                    'start_date' => now()->startOfYear(),
                    'end_date' => now()->endOfYear(),
                ]);
            }
        } else {
            $year = Year::create(['name' => $entryYear, 'status' => 'Inactive']);
        
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
            $this->flashError('Invalid Full Name', 'Name must be First Middle Last.');
            return null;
        }
    }

    private function generateUserUuid($academicYear, $studyModeId = null)
    {
        if ($studyModeId) {
            $studyMode = StudyMode::find($studyModeId);
            if ($studyMode) {
                $studyModeName = ucfirst(strtolower($studyMode->name));
            }
        }

        if ($studyModeName === 'Regular') {
            $studyModeName = 'R';
        } elseif ($studyModeName === 'Distance') {
            $studyModeName = 'D';
        } elseif ($studyModeName === 'Online') {
            $studyModeName = 'O';
        } elseif ($studyModeName === 'Extention') {
            $studyModeName = 'E';
        } else {
            $studyModeName = '';
        }

        $count = str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
        // last 2 digits of the year
        $academicYear = substr($academicYear, -2);
        return "SITS-$studyModeName-{$count}-{$academicYear}";
    }

    private function generateDefaultPassword($firstName, $phone)
    {
        $last4 = $phone ? substr($phone, -4) : '0000';
        return strtolower($firstName) . '@' . $last4;
    }

    private function formatPhone($phone)
    {
        if (!$phone) return '+251 900000000';

        if (str_starts_with($phone, '9')) {
            return '+251 ' . $phone;
        } elseif (str_starts_with($phone, '7')) {
            return '+254 ' . $phone;
        } elseif (str_starts_with($phone, '6') || str_starts_with($phone, '8')) {
            return '+27 ' . $phone;
        } elseif (!str_starts_with($phone, '0')) {
            // Remove leading 0 if present
            $phone = ltrim($phone, '0');
            return '+251 ' . $phone;
        }else{
            return $phone; // Already in correct format
        }
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
        // Normalize pastor phone number
        if ($pastorPhone && str_starts_with($pastorPhone, '9')) {
            $pastorPhone = '+251 ' . $pastorPhone;
        } elseif ($pastorPhone && str_starts_with($pastorPhone, '7')) {
            $pastorPhone = '+254 ' . $pastorPhone;
        } elseif ($pastorPhone && !str_starts_with($pastorPhone, '0')) {
            $pastorPhone = '+251 ' . $pastorPhone;
        } elseif (!$pastorPhone) {
            $pastorPhone = '+251 900000000'; // Default phone if not provided
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

    public function getExistingUserIds()
    {
        return User::whereIn('id', $this->existingUserIds)->get();
    }
}
