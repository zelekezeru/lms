<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Track;
use App\Models\Tenant;
use App\Models\Program;
use App\Http\Resources\TrackResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\YearResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\StatusResource;
use App\Models\Year;
use App\Models\User;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\Semester;
use App\Models\Section;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Student::with('user');

        // Apply search filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('middle_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('id_no', 'LIKE', "%{$search}%");
            });
        }

        // Set up sorting
        $allowedSorts = ['first_name', 'middle_name', 'last_name', 'id_no', 'created_at'];
        $sortColumn = $request->input('sortColumn', 'first_name');
        $sortDirection = $request->input('sortDirection', 'asc');

        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        // Paginate and transform
        $paginatedStudents = $query->paginate(30)->withQueryString();
        $students = StudentResource::collection($paginatedStudents);

        // Return with Inertia
        return inertia('Students/Index', [
            'students' => $students,
            'search' => $request->search,
            'sortInfo' => [
                "currentSortColumn" => $sortColumn,
                "currentSortDirection" => $sortDirection,
            ]
        ]);
    }


    public function show(Student $student)
    {
        // CHeck if student ststus is null
        if ($student->status === null) {
            $status = new Status();
            $status->student_id = $student->id;
            $status->user_id = Auth::user()->id; // Assuming you want to set the current user as the one who created the status
            $status->save();
        }

        $student = new StudentResource($student->load('user', 'courses', 'program', 'track', 'year', 'semester', 'section', 'church', 'status'));

        return Inertia::render('Students/Show', [
            'student' => $student,
            'user' => new UserResource($student->user),
            'status' => new StatusResource($student->status),
        ]);
    }

    public function create(): Response
    {
        $tracks = TrackResource::collection(Track::all());

        $programs = ProgramResource::collection(Program::all());

        $years = YearResource::collection(Year::all()->sortBy('name'));

        $semesters = SemesterResource::collection(Semester::all()->sortBy('name'));

        return inertia('Students/Create', [
            'tracks' => $tracks,
            'programs' => $programs,
            'years' => $years,
            'semesters' => $semesters,
        ]);
    }

    public function store(StudentStoreRequest $request)
    {
        // Validate the request
        $fields = $request->validated();

        // Generate student-specific data
        $fields['id_no'] = $this->student_id();
        $fields['tenant_id'] = Tenant::first()->id; // Assign tenant ID
        $student_email = $this->student_email($fields);

        // Create a new user for the student
        $user = $this->createStudentUser($fields, $student_email);

        // Link the user ID to the student fields
        $fields['user_id'] = $user->id;

        // Create the student record
        $student = $this->createStudent($fields);

        // Create related records (status and church)
        $this->createStudentStatus($student);
        $this->createStudentChurch($student, $fields);

        // Redirect to the student's show page with a success message
        return redirect(route('students.show', $student))->with('success', 'Student created successfully.');
    }

    /**
     * Create a new user for the student.
     */
    private function createStudentUser(array $fields, string $student_email): User
    {
        $user_phone = substr($fields['mobile_phone'], -4);
        $default_password = $fields['first_name'] . '@' . $user_phone;

        $user_data = [
            'name' => $fields['first_name'] . ' ' . $fields['middle_name'] . ' ' . $fields['last_name'],
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
        $status = new Status();
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

    public function edit(Student $student): Response
    {
        $tracks = TrackResource::collection(Track::all());

        $programs = ProgramResource::collection(Program::all());

        $years = YearResource::collection(Year::all());

        $semesters = SemesterResource::collection(Semester::all());

        return Inertia::render('Students/Edit', [
            'student' => $student,
            'tracks' => $tracks,
            'programs' => $programs,
            'years' => $years,
            'semesters' => $semesters,
        ]);
    }

    public function update(StudentUpdateRequest $request, Student $student)
    {
        // Validate the request
        $fields = $request->validated();

        // Update the associated user record
        $this->updateStudentUser($student, $fields);

        // Update the student record
        $this->updateStudentRecord($student, $fields);

        // Redirect to the student's show page with a success message
        return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
    }

    /**
     * Update the associated user record for the student.
     */
    private function updateStudentUser(Student $student, array $fields): void
    {
        $user = $student->user; // Assuming a relationship exists between Student and User

        if ($user) {
            $name = $fields['first_name'] . ' ' . $fields['middle_name'] . ' ' . $fields['last_name'];
            $student_email = $this->student_email($fields);

            $user->update([
                'name' => $name,
                'email' => $student_email,
                'phone' => $fields['mobile_phone'],
            ]);
        }
    }

    /**
     * Update the student record.
     */
    private function updateStudentRecord(Student $student, array $fields): void
    {
        // Ensure the user ID is linked
        $fields['user_id'] = $student->user->id ?? null;

        // Update the student record
        $student->update($fields);
    }

    public function destroy(Student $student)
    {
        // Soft delete the student
        $student->is_deleted = true;
        $student->deleted_at = now();
        $student->deleted_by_name = Auth::user()->id;
        $student->save();
        // Optionally, you can also delete the associated user record
        $user = $student->user;
        if ($user) {
            $user->is_deleted = true;
            $user->deleted_at = now();
            $user->deleted_by_name = Auth::user()->id;
            $user->save();
        }

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function verify(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $status = $student->status;

        if (!$status) {
            // Create a new status record if it doesn't exist
            $status = new Status();
            $status->student_id = $student->id;
            $status->user_id = $student->user->id; // Assuming you want to set the current user as the one who created the status
        }

        $statuses = [
            'active',
            'approved',
            'completed',
            'verified',
            'enrolled',
            'graduated',
            'scholarship',
            'scholarship_approved',
            'scholarship_verified'
        ];

        // Check if any of the status fields are present in the request
        foreach ($statuses as $statusField) {
            if ($request->has('is_'.$statusField)) {
                // Check if the status field is already set to 1
                if ($status->{'is_' . $statusField} == 1) {
                    // If it's already 1, set it to 0
                    $status->{'is_' . $statusField} = 0;
                } else {
                    // If it's not set, set it to 1
                    $status->{'is_' . $statusField} = 1;
                }
                $status->{$statusField . '_by_name'} = Auth::user()->name;
                $status->{$statusField . '_at'} = now();
            }
        }
        $status->save();
        // Return a success response
        return redirect()->route('students.show', $student)->with('success', 'Student status updated successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $students = Student::where('first_name', 'like', "%$search%")
            ->orWhere('student_id', 'like', "%$search%")
            ->latest()
            ->paginate(15);
        return Inertia::render('Students/Index', compact('students'));
    }

    public function student_id()
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two di

        $tenant = substr(Tenant::first()->name, -1); // get the first tenant name

        $userUuid = $tenant . '-' . $year . '-' . 'ST-' . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);

        return $userUuid;
    }

    public function student_email($fields)
    {
        $username = $fields['first_name'] . ' ' . $fields['middle_name'];

        $email = strtolower(str_replace(' ', '.', $username)) . '@sits.edu.et';

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
            //Academic details
            'year_id' => $fields['year_id'],
            'semester_id' => $fields['semester_id'],
            'program_id' => $fields['program_id'],
            'track_id' => $fields['track_id'],

            //Church details
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
}
