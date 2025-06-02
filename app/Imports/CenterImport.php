<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Center;
use App\Http\Resources\CenterResource;
use App\Models\User;
use App\Models\Student;
use App\Models\Status;
use App\Models\Semester;
use App\Models\Year;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class CenterImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    protected $center_id;
    protected $program_id;
    protected $track_id;
    protected $study_mode_id;
    protected $year_id;
    protected $semester_id;
    protected $tenant_id;
    protected $user_id;

    public function __construct($center_id, $study_mode_id)
    {
        $this->center_id = $center_id;
        $this->study_mode_id = $study_mode_id;
    }

    public function collection(Collection $rows)
    {
        $center = Center::findOrFail($this->center_id);

        $center = new CenterResource($center->load([
            'user', 'program', 'track', 'year', 'semester', 'studyMode',
        ]));

        $this->program_id = $center->program->id;
        $this->track_id = $center->track->id;
        $this->study_mode_id = $center->studyMode->id;
        $this->tenant_id = $center->user->tenant_id;
        $this->user_id = $center->user->id;

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {

                if (! isset($row['full_name']) || ! isset($row['phone']) || ! isset($row['sex'])) {
                    Session::flash('sweet_alert', [
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

                if (User::where('email', $email)->exists()) {
                    Session::flash('sweet_alert', [
                        'type' => 'error',
                        'title' => 'Duplicate User & Email',
                        'text' => 'User with email '.$email.' & Name already exists. Skipping this entry.',
                    ]);
                    continue;
                }

                $year = DB::table('years')->where('name', $row['entry_year'])->first();

                if ($year) {
                    $semester = Semester::where('year_id', $year->id)->first();
                    $academicYear = substr($year->name, -2);
                } else {
                    $year = Year::firstOrCreate([
                        'name' => $row['entry_year'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $semester = Semester::firstOrCreate([
                        'name' => '1st - ' . $year->name,
                        'year_id' => $year->id,
                        'level' => 1,
                        'start_date' => now()->startOfYear(),
                        'end_date' => now()->endOfYear(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $academicYear = substr($row['entry_year'], -2);
                }

                $studentCount = str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
                $userUuid = 'SITS-'.str_pad($studentCount, 4, '0', STR_PAD_LEFT).'-'.$academicYear;
                $default_password = strtolower($firstName).'@'.substr($row['phone'], -4);

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
                    'center_id' => $this->center_id,
                    'year_id' => $year->id,
                    'semester_id' => $semester->id,
                    'tenant_id' => $this->tenant_id,
                    'user_id' => $user->id,
                ]);

                $this->createStudentStatus($student);

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
