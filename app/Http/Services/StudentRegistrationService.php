<?php

namespace App\Http\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Models\Status;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentRegistrationService extends Controller
{
    public function store(StudentStoreRequest $request)
    {
        // Validate the request
        $fields = $request->validated();

        // Generate student-specific data
        $fields['id_no'] = $this->student_id();
        $fields['tenant_id'] = Tenant::first()->id; // Assign tenant ID
        $student_email = $this->student_email($fields);

        //Date of Birth
        $dateOfBirth = $request->input('date_of_birth');
        $fields['date_of_birth'] = Carbon::parse($dateOfBirth)->format('Y-m-d');

        // Create a new user for the student
        $user = $this->createStudentUser($fields, $student_email);
        $user->assignRole('STUDENT');

        // Link the user ID to the student fields
        $fields['user_id'] = $user->id;

        // Create the student record
        $student = $this->createStudent($fields);

        // Create related records (status and church)
        $this->createStudentStatus($student);
        $this->createStudentChurch($student, $fields);

        return $student;
    }

    /**
     * Create a new user for the student.
     */
    private function createStudentUser(array $fields, string $student_email): User
    {
        $user_phone = substr($fields['mobile_phone'], -4);
        $default_password = $fields['first_name'].'@'.$user_phone;

        $user_data = [
            'name' => $fields['first_name'].' '.$fields['middle_name'].' '.$fields['last_name'],
            'email' => $student_email,
            'phone_number' => $fields['mobile_phone'],
            'password' => bcrypt($default_password),
            'default_password' => $default_password,
            'user_uuid' => $fields['id_no'],
            'phone' => $fields['mobile_phone'],
        ];

        return User::create($user_data);
    }

    /**
     * Create a new Student.
     */
    private function createStudent(array $fields): Student
    {
        $student_data = [
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'],
            'last_name' => $fields['last_name'],
            'mobile_phone' => $fields['mobile_phone'],
            'office_phone' => $fields['office_phone'],
            'date_of_birth' => $fields['date_of_birth'],
            'marital_status' => $fields['marital_status'],
            'sex' => $fields['sex'],
            'address' => $fields['address'],
            'program_id' => $fields['program_id'],
            'track_id' => $fields['track_id'],
            'study_mode_id' => $fields['study_mode_id'],
            'year_id' => $fields['year_id'],
            'semester_id' => $fields['semester_id'],
            'id_no' => $fields['id_no'],
            'tenant_id' => $fields['tenant_id'],
            'user_id' => $fields['user_id'],
            'updated_at' => now(),
            'created_at' => now(),
        ];
        // Ensure the user ID is linked
        $fields['user_id'] = $fields['user_id'] ?? null;

        // Create the student record
        return Student::create($student_data);
    }

    /**
     * Create a status record for the student.
     */
    private function createStudentStatus(Student $student): void
    {
        $status = new Status;
        $status->student_id = $student->id;
        $status->user_id = $student->user->id; // Link to the user who created the status
        $status->is_active = true; // Default status is active
        $status->created_by_name = Auth::user()->name; // Set the creator's name
        $status->created_at = now(); // Set the creation timestamp
        $status->save();
    }

    /**
     * Create a church record for the student.
     */
    private function createStudentChurch(Student $student, array $fields): void
    {
        $church_data = [
            'student_id' => $student->id,
            'pastor_name' => $fields['pastor_name'],
            'pastor_phone' => $fields['pastor_phone'],
            'position_denomination' => $fields['position_denomination'],
            'church_name' => $fields['church_name'],
            'church_address' => $fields['church_address'],
        ];

        $student->church()->create($church_data);
    }

    public function update(StudentUpdateRequest $request, Student $student)
    {
        // Validate the request
        $fields = $request->validated();

        // Update the associated user record
        $this->updateStudentUser($student, $fields);

        // Update the student record using the same data structure as used in creation
        $student->update($this->update_student_data($fields));

        // Update or create the church record to follow the same logic as in createStudentChurch()
        if (isset($fields['church_name']) && $fields['church_name']) {
            if ($student->church) {
                $student->church->update([
                    'pastor_name' => $fields['pastor_name'],
                    'pastor_phone' => $fields['pastor_phone'],
                    'position_denomination' => $fields['position_denomination'],
                    'church_name' => $fields['church_name'],
                    'church_address' => $fields['church_address'],
                ]);
            } else {
                $student->church()->create([
                    'pastor_name' => $fields['pastor_name'],
                    'pastor_phone' => $fields['pastor_phone'],
                    'position_denomination' => $fields['position_denomination'],
                    'church_name' => $fields['church_name'],
                    'church_address' => $fields['church_address'],
                ]);
            }
        }

        // Update or create the status record similarly as stored in createStudentStatus()
        if ($student->status) {
            $student->status->update([
                'is_active' => true, // Assuming you want to keep it active
                'updated_by_name' => Auth::user()->name,
                'updated_at' => now(),
            ]);
        } else {
            $this->createStudentStatus($student);
        }
        // Update or create the Church record

        if (isset($fields['church_name']) && $fields['church_name']) {
            if ($student->church) {
                $student->church->update([
                    'pastor_name' => $fields['pastor_name'],
                    'pastor_phone' => $fields['pastor_phone'],
                    'position_denomination' => $fields['position_denomination'],
                    'church_name' => $fields['church_name'],
                    'church_address' => $fields['church_address'],
                ]);
            } else {
                $this->createStudentChurch($student, $fields);
            }
        }

        Log::info('Student and related records created successfully.', ['student_id' => $student->id]);

        return $student;
    }

    /**
     * Update the associated user record for the student.
     */
    private function updateStudentUser(Student $student, array $fields): void
    {
        $user = $student->user; // Assuming a relationship exists between Student and User

        if ($user) {
            $name = $fields['first_name'].' '.$fields['middle_name'].' '.$fields['last_name'];
            $student_email = $this->student_email($fields);

            $user->update([
                'name' => $name,
                'email' => $student_email,
                'phone' => $fields['mobile_phone'],
            ]);
        }
    }

    public function student_id()
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two di

        $tenant = substr(Tenant::first()->name, -1); // get the first tenant name

        do {
            $initialCount = $count ?? Student::max('id');
            $count = $initialCount + 1; // safer than count()
            $studentUuid = 'SITS-' . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . $year;
        } while (User::where('user_uuid', $studentUuid)->exists());

        return $studentUuid;
    }

    public function student_email($fields)
    {
        $username = $fields['first_name'].' '.$fields['middle_name'];

        $email = strtolower(str_replace(' ', '.', $username)).'@sits.edu.et';

        return $email;
    }

    public function student_data($fields)
    {
        $student_data = [
            // Personal details
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'],
            'last_name' => $fields['last_name'],
            'mobile_phone' => $fields['mobile_phone'],
            'office_phone' => $fields['office_phone'],
            'date_of_birth' => $fields['date_of_birth'],
            'marital_status' => $fields['marital_status'],
            'sex' => $fields['sex'],
            'address' => $fields['address'],
            // Academic details
            'year_id' => $fields['year_id'],
            'semester_id' => $fields['semester_id'],
            'program_id' => $fields['program_id'],
            'track_id' => $fields['track_id'],

            // Church details
            'pastor_name' => $fields['pastor_name'],
            'pastor_phone' => $fields['pastor_phone'],
            'position_denomination' => $fields['position_denomination'],
            'church_name' => $fields['church_name'],
            'church_address' => $fields['church_address'],

            'id_no' => $fields['id_no'],

            // ID card details
            'user_id' => $fields['user_id'],
            'tenant_id' => $fields['tenant_id'],

        ];

        return $student_data;
    }

    public function update_student_data($fields)
    {
        $student_data = [
            // Personal details
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'],
            'last_name' => $fields['last_name'],
            'mobile_phone' => $fields['mobile_phone'],
            'office_phone' => $fields['office_phone'],
            'date_of_birth' => $fields['date_of_birth'],
            'marital_status' => $fields['marital_status'],
            'sex' => $fields['sex'],
            'address' => $fields['address'],
            'year_id' => $fields['year_id'],
            'semester_id' => $fields['semester_id'],
            'program_id' => $fields['program_id'],
            'track_id' => $fields['track_id'],
        ];

        return $student_data;
    }
}
