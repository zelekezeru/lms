<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Department;
use App\Models\Tenant;
use App\Models\Program;
use App\Http\Resources\DepartmentResource;
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
use App\Models\Year;
use App\Models\User;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\Semester;
use App\Models\Section;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Student::query();

        // Apply search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where('student_name', 'LIKE', "%{$search}%")
                  ->orWhere('father_name', 'LIKE', "%{$search}%")
                  ->orWhere('grand_father_name', 'LIKE', "%{$search}%")
                  ->orWhere('id_no', 'LIKE', "%{$search}%");
        }

        // Apply sorting
        $allowedSorts = ['student_name', 'id_no', 'program_id'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;
        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        // Paginate results
        $students = $query->paginate(30)->withQueryString();

        $students = StudentResource::collection($query->paginate(30)->withQueryString());

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
        $student = new StudentResource($student->load('user'));
        
        return Inertia::render('Students/Show', [            
            'student' => $student,
            'program' => $student->program,
            'department' => $student->department,
            'year' => $student->year,
            'semester' => $student->semester,
            'section' => $student->section,
            'user' => $student->user,
        ]);
    }
    

    public function create(): Response
    {
        $departments = DepartmentResource::collection(Department::all());

        $programs = ProgramResource::collection(Program::all());

        $years = YearResource::collection(Year::all()->sortBy('name'));

        $semesters = SemesterResource::collection(Semester::all()->sortBy('name'));

        return inertia('Students/Create', [
            'departments' => $departments,
            'programs'=> $programs,
            'years' => $years,
            'semesters' => $semesters,
        ]);
    }
    
    public function store(StudentStoreRequest $request)
    {
        $fields = $request->validated(); 

        // Create a new Tenant Admin in User table
        $user_phone = substr($fields['mobile_phone'], -4);

        $default_password = $fields['student_name'] . '@' . $user_phone;
        
        $fields['id_no'] = $this->student_id();

        $name = $fields['student_name']. ' ' . $fields['father_name'] . ' ' . $fields['grand_father_name'];
        
        $fields['tenant_id'] = Tenant::first()->id; // Updated to use tenant ID
        
        $student_email = $this->student_email($fields);
        
        $user_data = [
            'name'=> $name,
            'email'=> $student_email,
            'phone_number'=> $fields['mobile_phone'],
            'password'=> bcrypt($default_password),
            'default_password' => $default_password,
            'user_uuid' => $fields['id_no'],
            'phone' => $fields['mobile_phone'],
        ];

        $user = User::create($user_data);        

        $fields['user_id'] = $user->id;
        
        $student = Student::create($fields);  

        // Create the student
        return redirect(route('students.show', $student))->with('success', 'Student created successfully.');
    }

    public function edit(Student $student): Response
    {
        $departments = DepartmentResource::collection(Department::all());

        $programs = ProgramResource::collection(Program::all());

        $years = YearResource::collection(Year::all());

        $semesters = SemesterResource::collection(Semester::all());

        return Inertia::render('Students/Edit', [
            'student' => $student,
            'departments' => $departments,
            'programs' => $programs,
            'years' => $years,
            'semesters' => $semesters,
        ]);
    }

    public function update(StudentStoreRequest $request, Student $student)
    {
        $fields = $request->validated();

        // Update the associated user record
        $user = $student->user; // Assuming a relationship exists between Student and User

        if ($user) {
            $name = $fields['student_name'] . ' ' . $fields['father_name'] . ' ' . $fields['grand_father_name'];
            $student_email = $this->student_email($fields);

            $user->update([
                'name' => $name,
                'email' => $student_email,
                'phone' => $fields['mobile_phone'],
            ]);
        }

        // Update the student record
        $fields['user_id'] = $user->id; // Ensure the user ID is linked

        $student->update($fields);

        return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
    }

    //Student goto Profile
    public function profile(Student $student)
    {        
        $user = UserResource::collection(User::where('id', $student->user_id)->get());
        
        return inertia('Students/Profile', [
            'student' => new StudentResource($student),
            'user' => $user,
        ]);

    }
    //Student Profile Image and Status
    public function updateProfile(Request $request, Student $student)
    {
        $request->validate([
            'profile_img' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($request->hasFile('profile_img')) {
            $imagePath = $request->file('profile_img')->store('profile_images', 'public');
            $student->user->update(['profile_img' => $imagePath]);
        }

        // Update the status of the student
        if ($request->has('status')) {
            $student->update(['status' => $request->input('status')]);
        }

        return redirect()->route('students.show', $student)->with('success', 'Profile image updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $students = Student::where('student_name', 'like', "%$search%")
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

    public function student_email($fields){
        $username = $fields['student_name']. ' ' . $fields['father_name'];

        $email = strtolower(str_replace(' ', '.', $username)) . '@sits.edu.et';

        return $email;
    }

    public function student_data($fields)
    {
        $student_data = [
            // Personal details
            'student_name' => $fields['student_name'],
            'father_name' => $fields['father_name'],
            'grand_father_name' => $fields['grand_father_name'],
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
            'department_id' => $fields['department_id'],
            
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
