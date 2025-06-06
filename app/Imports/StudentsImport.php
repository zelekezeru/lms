<?php

namespace App\Imports;

use App\Http\Resources\SectionResource;
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
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Add this import if SectionResource exists in this namespace
use Carbon\Carbon;

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
        $this->tenant_id = $section->user->tenant_id; // assuming user relation carries tenant
        $this->user_id = $section->user->id;

        DB::transaction(function () use ($rows) {
            
            foreach ($rows as $row) {

                // Validate row data
                if (! isset($row['full_name']) || ! isset($row['phone']) || ! isset($row['sex'])) {
                    session()->flash('sweet_alert', [
                        'type' => 'error',
                        'title' => 'Missing Required Fields',
                        'text' => 'Some required fields are missing in this row. Skipping this entry.',
                    ]);

                    continue;
                }

                $fullNameParts = explode(' ', $row['full_name']);
                $firstName = $fullNameParts[0] ?? '';
                $middleName = $fullNameParts[1] ?? '';
                $lastName = $fullNameParts[2] ?? '';

                $email = strtolower(Str::slug($firstName).'.'.Str::slug($middleName)).'@sits.edu.et';

                // Check if the email already exists
                if (User::where('email', $email)->exists()) {
                    // If email exists, skip this row

                    session()->flash('sweet_alert', [
                        'type' => 'error',
                        'title' => 'Duplicate User & Email',
                        'text' => 'User with email '.$email.' & Name already exists. Skipping this entry.',
                    ]);

                    continue;
                }
                // Fetch the Id of Year from the list of Academic years that matches the imported entry_year and fetch the id
                $year = DB::table('years')->where('name', $row['entry_year'])->first();

                // If year is not found, Create a new year entry
                if ($year) {
                    $semester = Semester::where('year_id', $year->id)->first();
                    // Assuming there is a relationship, but since $year is a stdClass, use query
                    if( $semester != null) {
                        $semester = Semester::where('year_id', $year->id)->first();
                    } else {
                        // Create a new semester for the existing year
                        $semester = Semester::firstOrCreate([
                            'name' => '1st - '.$year->name,
                            'year_id' => $year->id,
                            'level' => 1,
                            'start_date' => now()->startOfYear(),
                            'end_date' => now()->endOfYear(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $academicYear = substr($year->name, -2); // Get last two digits of the current year
                } else {
                    // Create a new year entry if it doesn't exist
                    $year = Year::firstOrCreate([
                        'name' => $row['entry_year'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Create a semester for the year if it doesn't exist
                    $semester = Semester::firstOrCreate([
                        'name' => '1st - '.$year->name,
                        'year_id' => $year->id,
                        'level' => 1,
                        'start_date' => now()->startOfYear(),
                        'end_date' => now()->endOfYear(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $academicYear = substr($row['entry_year'], -2); // Get last two digits of the entry year
                }

                // 👤 Generate custom user_uuid
                $studentCount = str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);

                $userUuid = 'SITS-'.str_pad($studentCount, 4, '0', STR_PAD_LEFT).'-'.$academicYear;

                $default_password = strtolower($firstName).'@'.substr($row['phone'], -4); // Default password for new users

                // Verify Date o Birth format                

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'user_uuid' => $userUuid,
                        'name' => $firstName.' '.$middleName,
                        'email' => $email,
                        'password' => Hash::make($default_password),
                        'default_password' => $default_password,
                    ]
                );

                $data = [];
                // If user already exists, update the user data
                if ($userUuid) {
                    $data['id_no'] = $userUuid;
                }
                if ($firstName) {
                    $data['first_name'] = $firstName.' '.$middleName;
                }
                if ($middleName) {
                    $data['middle_name'] = $middleName;
                }
                if ($lastName) {
                    $data['last_name'] = $lastName;
                }
                if (isset($row['sex'])) {
                    $data['sex'] = $row['sex'];
                }
                if (isset($row['phone'])) {
                    // If the first character of phone mumber is 9 add 0 before it                    
                    $phone = $row['phone'];
                    if (str_starts_with($phone, '9')) {
                        $phone = '0' . $phone;
                    }
                    $data['mobile_phone'] = $phone;
                }                           
                if (isset($row['address'])) {
                    $data['address'] = $row['address'];
                }
                $data['date_of_birth'] = null;

                if (!empty($row['date_of_birth'])) {
                    try {
                        // Attempt to parse the date using Carbon's flexible parser
                        $dateOfBirth = Carbon::parse($row['date_of_birth']);

                        // Format it to strict 'Y-m-d' for saving in DB
                        $data['date_of_birth'] = $dateOfBirth->format('Y-m-d');
                    } catch (\Exception $e) {
                        // If parsing fails, set as null
                        $data['date_of_birth'] = null;
                    }
                }
                if ($this->program_id) {
                    $data['program_id'] = $this->program_id;
                }
                if ( $this->track_id) {
                    $data['track_id'] = $this->track_id;
                }
                if ($this->study_mode_id) {
                    $data['study_mode_id'] = $this->study_mode_id;
                }
                if ($this->section_id) {
                    $data['section_id'] = $this->section_id;
                }
                if ($year) {
                    $data['year_id'] = $year->id;
                }
                if ($semester) {
                    $data['semester_id'] = $semester->id;
                }
                if ($this->tenant_id) {
                    $data['tenant_id'] = $this->tenant_id;
                }
                if ($user->id) {
                    $data['user_id'] = $user->id;
                }
                
                // 👨‍🎓 Create Student
                $student = Student::updateOrCreate($data);

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
        if (isset($fields['pastor_phone'])) {                   
            $pastor_phone = $fields['pastor_phone'];
            if (str_starts_with($pastor_phone, '9')) {
                $pastor_phone = '0' . $pastor_phone;
            }
        }else{
            $pastor_phone = null;
        }
        $church_data = [
            'student_id' => $student->id,
            'pastor_name' => $fields['pastor_name'] ?? null,
            'pastor_phone' => $pastor_phone,
            'position_denomination' => $fields['position_denomination'] ?? null,
            'church_name' => $fields['church_name'] ?? null,
            'church_address' => $fields['church_address'] ?? null,
        ];

        $student->church()->create($church_data);
    }
}
