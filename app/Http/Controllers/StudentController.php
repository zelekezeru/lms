<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Department;
use App\Models\Tenant;
use App\Models\Program;
use App\Http\Resources\DepartmentResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\YearResource;
use App\Models\Year;
use App\Models\User;
use App\Http\Controllers\Auth\RegisteredUserController;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        // Fetch the search term from the request
        $search = $request->input('search', '');
        
        // Query the students, applying the search filter if provided
        $students = Student::latest()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_name', 'LIKE', "%{$search}%")
                          ->orWhere('father_name', 'LIKE', "%{$search}%")
                          ->orWhere('grand_father_name', 'LIKE', "%{$search}%")
                          ->orWhere('program', 'LIKE', "%{$search}%");
                });
            })
            ->paginate(15)
            ->withQueryString(); // Retain the search term for pagination links
        
        return Inertia::render('Students/Index', [
            'students' => $students,
            'search' => $search, // Pass the search term back to the frontend
        ]);
    }
    
    public function show(Student $student)
    {

        return Inertia::render('Students/Show', [
            'student' => $student,
        ]);
    }
    

    public function create(): Response
    {
        $departments = DepartmentResource::collection(Department::all());

        $programs = ProgramResource::collection(Program::all());

        $years = YearResource::collection(Year::all());

        return inertia('Students/Create', [
            'departments' => $departments,
            'programs'=> $programs,
            'years' => $years,
        ]);
    }
    
    public function store(StudentRequest $request)
    {
        $fields = $request->validated(); 

        // Create a new Tenant Admin in User table
        $user_phone = substr($fields['mobile_phone'], -4);

        $fields['default_password'] = $fields['student_name'] . '@' . $user_phone;
        
        $fields['id_no'] = $this->student_id();
dd($fields);
        $student = Student::create($fields);   
        
        $user = User::create([
            'name'=> $fields['name'],
            'email'=> $fields['email'],
            'password'=> bcrypt($fields['default_password']),
            'default_password' => $fields['default_password'], 

        ]);
        // Create the student
        return redirect()->route('students.show', $student)->with('success', 'Student created successfully.');
    }

    public function edit(Student $student): Response
    {
        $departments = DepartmentResource::collection(Department::all());
        $programs = ProgramResource::collection(Program::all());
        $years = YearResource::collection(Year::all());

        return Inertia::render('Students/Edit', [
            'student' => $student,
            'departments' => $departments,
            'programs' => $programs,
            'years' => $years,
        ]);
    }

    public function update(StudentRequest $request, Student $student)
    {
        $fields = $request->validated();

        // Update the student record
        $student->update($fields);

        // Update the associated user record if necessary
        $user = $student->user; // Assuming a relationship exists between Student and User
        if ($user) {
            $user->update([
                'name' => $fields['name'],
                'email' => $fields['email'],
            ]);
        }

        return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
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
}
