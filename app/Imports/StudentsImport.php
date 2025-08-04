<?php

namespace App\Imports;

use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Status;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use App\Models\StudyMode;
use App\Models\Track;
use App\Models\CourseOffering;
use App\Models\Center;
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
    protected $existingUserIds = [];

    public function __construct(protected $section_id, protected $center_id, protected $request) {
        if($section_id !== null) {
            $section = Section::find($section_id);
            $center = null;
        } elseif($center_id !== null) {
            $center = Center::find($center_id);
        } else {
            $section = null;
            $center = null;
        }
        $this->section = $section;
        $this->center = $center;
        
    }
    
    public function collection(Collection $rows)
    {
        // Load the center and coordinator with user
        if ($this->center_id) {
            // Ensure the center exists
            $center = Center::with('coordinator.user')->findOrFail($this->center_id);
        } else {
            // If no center_id is provided, we can skip this part
            $center = null;
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                if(!isset($row['full_name']) || !isset($row['program']) || !isset($row['entry_year'])) {
                    $this->notRegisteredCount++;
                    continue;
                }
                
                [$firstName, $middleName, $lastName] = $this->parseFullName($row['full_name']);

                // Resolve program by code or name

                $program = $this->resolveProgram($row['program'] ?? null, $row['study_mode'] ?? null);

                if (!$program) {
                    $this->notRegisteredCount++;
                    continue;
                }
                // Initialize section if not already set
                [$year, $semester, $academicYear] = $this->getOrCreateYearAndSemester($row['entry_year']);
                $track = $program->tracks()->first();
                $section = $this->sectionAssigned($track, $year);

                if (!$section) {
                    $this->notRegisteredCount++;
                    continue;
                }

                $phone = $this->formatPhone($row['phone'] ?? '');
                
                $email = $this->generateEmail($firstName, $middleName);

                $existingUser = User::where('email', $email)->where('phone', $phone)->first();

                if ($existingUser) {
                    $this->duplicateData++;
                    $this->existingUserIds[] = $existingUser->id;
                    continue;
                }

                $userUuid = $this->generateUserUuid($academicYear, $this->study_mode_id ?? 1);

                $defaultPassword = $this->generateDefaultPassword($firstName, $phone);
                
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'user_uuid' => $userUuid,
                        'phone' => $phone,
                        'tenant_id' => $this->tenant_id,
                        'name' => "$firstName $middleName",
                        'password' => Hash::make($defaultPassword),
                        'default_password' => $defaultPassword,
                    ]
                );
                
                $student = Student::updateOrCreate(
                    ['user_id' => $user->id],
                    $this->prepareStudentData($row, $firstName, $middleName, $lastName, $userUuid, $year, $semester, $user->id, $phone, $section->id)
                );

                $user->assignRole('STUDENT');

                $this->createStudentStatus($student);

                if (!empty($row['pastor_name']) || !empty($row['church_name'])) {
                    $this->createStudentChurch($student, $row->toArray());
                }

                // 7. Attach center if not attached
                if($this->center_id !== null) {
                    $student->centers()->syncWithoutDetaching([$this->center_id]);
                }

                $this->registeredCount++;
                $this->registeredStudentIds[] = $student->id;
            }
        });
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
            $studyMode = StudyMode::where('name', 'REGULAR')->first();
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

            $this->assignTrackCoursesToSection($section);
        }

        return $section;
    }

    public function assignTrackCoursesToSection(Section $section)
    {
        $track = Track::where('id', $section->track_id)->first();

        $fields = [
            'track_id' => $track->id,
            'study_mode_id' => $section->study_mode_id,
        ];
        // Assign the track to the section

        $trackCourses = $track->courses()->with(['curricula' => function ($q) use ($fields) {
            return $q->where('track_id', $fields['track_id'])->where('study_mode_id', $fields['study_mode_id']);
        }])->get();

        foreach ($trackCourses as $trackCourse) {
            $curriculum = $trackCourse->curricula->first();
            if ($curriculum !== null) {
                $fields['year_level'] = $curriculum->year_level;
                $fields['semester_level'] = $curriculum->semester_level;

                CourseOffering::updateOrCreate(
                    [
                        'course_id' => $trackCourse->id,
                        'section_id' => $section->id,
                    ],
                    [
                        'year_level' => $curriculum->year_level ?? null,
                        'semester_level' => $curriculum->semester_level ?? null,
                    ],
                );
            }
        }

        return redirect()->route('sections.show', $section->id)->with('success', 'Track assigned to section successfully.');
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
            return [null, null, null];
        }
    }

    protected function generateEmail($firstName, $middleName)
    {
        return strtolower(Str::slug($firstName) . '.' . Str::slug($middleName)) . '@sits.edu.et';
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

    protected function generateDefaultPassword($firstName, $phone)
    {        
        $last4 = substr($phone, -4);
        return strtolower($firstName) . '@' . $last4;
    }

    // Prepare student data for creation or update
    protected function prepareStudentData($row, $firstName, $middleName, $lastName, $userUuid, $year, $semester, $userId, $phone, $sectionId)
    {
        return [
            'id_no' => $userUuid,
            'old_id' => $row['old_id'] ?? null,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'sex' => isset($row['sex']) ? strtoupper($row['sex']) : '',
            'mobile_phone' => $phone ?? '+251900000000',
            'address' => $row['address'] ?? null,
            'date_of_birth' => $this->parseDateOfBirth($row['date_of_birth'] ?? null),
            'program_id' => $this->program_id,
            'track_id' => $this->track_id,
            'study_mode_id' => $this->study_mode_id,
            'section_id' => $sectionId,
            'year_id' => $year->id,
            'semester_id' => $semester->id,
            'tenant_id' => $this->tenant_id,
            'user_id' => $userId,
        ];
    }

    // Parse date of birth from various formats
    protected function parseDateOfBirth($date)
    {
        if (!$date) return null;
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    // Format phone numbers to international format
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
        return User::whereIn('id', $this->registeredStudentIds)->orderBy('name')->get();
    }

    public function getExistingUserIds()
    {
        return User::whereIn('id', $this->existingUserIds)->orderBy('name')->get();
    }
}
