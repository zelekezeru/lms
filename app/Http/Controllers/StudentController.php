<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
    

    public function create(): Response
    {
        $departments = DepartmentResource::collection(Department::all());

        return inertia('Students/Create', [
            'departments' => $departments,
        ]);
    }
    
    public function show(Student $student)
    {

        return Inertia::render('Students/Show', [
            'student' => $student,
        ]);
    }
    
    public function store(StudentRequest $request)
    {
        
        $student = Student::create($request->validated());
        
        $tenant = 'SITS';

        $student_id = $this->student_id($tenant, $student->program);

        $student->update(['student_id' => $student_id]);

        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    public function edit(Student $student): Response
    {
        return Inertia::render('Students/Edit', compact('student'));
    }

    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
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

    public function student_id($tenant, $program)
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two digits

        if($program == 'Regular') {
            $program_ref = 'RG';
        } elseif($program == 'Online') {
            $program_ref = 'OL';
        } elseif($program == 'Distance') {
            $program_ref = 'DS';
        }else {
            return  'Invalid program';
        }

        $student_id = $tenant . '/' . $program_ref . ' ' . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT) . '/' . $year;

        return $student_id;
    }
}
