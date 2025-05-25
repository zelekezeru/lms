<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\StudentRegistrationController;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\StatusResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\TrackResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\YearResource;
use App\Http\Resources\StudyModeResource;
use App\Http\Resources\UserDocumentResource;
use App\Http\Services\StudentRegistrationService;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\PaymentMethod;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Status;
use App\Models\Student;
use App\Models\StudyMode;
use App\Models\Track;
use App\Models\User;
use App\Models\Year;
use App\Models\SemesterStudent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

use function PHPSTORM_META\map;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Student::with('user');

        // Apply search filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;

            $query->whereHas('status', function ($q) {
                $q->where('is_deleted', false);
            })->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('middle_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('id_no', 'LIKE', "%{$search}%");
            });
        }

        // Set up sorting
        $allowedSorts = ['first_name', 'middle_name', 'last_name', 'id_no', 'contact_phone'];
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
                'currentSortColumn' => $sortColumn,
                'currentSortDirection' => $sortDirection,
            ],
        ]);
    }

    public function show(Student $student)
    {
        // CHeck if student status is null
        if ($student->status === null) {
            $status = new Status;
            $status->student_id = $student->id;
            $status->user_id = Auth::user()->id; // Assuming you want to set the current user as the one who created the status
            $status->save();
        }

        $student = new StudentResource($student->load('user', 'courses', 'program', 'track', 'year', 'semester', 'section', 'church', 'status', 'results', 'grades', 'payments', 'studyMode'));

        $yearLevel = $student->section->yearLevel();
        $semester = $student->section->semester->level;

        $studyModes = StudyMode::with(['sections' => function ($query) use ($yearLevel, $semester) {
            $query->whereHas('courseSectionAssignments', function ($q) use ($yearLevel, $semester) {
                $q->where('year_level', $yearLevel)
                    ->where('semester', $semester);
            })->with(['courseSectionAssignments' => function ($q) use ($yearLevel, $semester) {
                $q->where('year_level', $yearLevel)
                    ->where('semester', $semester);
            }]);
        }])->get();

        $studyModes = StudyModeResource::collection($studyModes);

        // Check if the student has a section & Fetch courses accordingly
        if ($student->section === null) {
            $sections = Section::where('program_id', $student->program->id)
                ->get()->load('program', 'courses');
            $courses = [];
        } else {
            $courses = $student->section->courses->sortBy('name')->values();
            $sections = [];
        }

        // Fetch Payment Categories and Methods
        $paymentCategories = PaymentCategory::where('is_active', true)->get();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        // Fetch the payments for the student
        $payments = Payment::where('student_id', $student->id)
            ->with(['paymentMethod', 'paymentCategory'])
            ->get();

        $user = new UserResource($student->user->load('userDocuments'));

        // For Transcript
        $student->load([
            'user',
            'program',
            'track',
            'studyMode',
            'year',
            'semester',
            'section',
        ]);

        $semesters = $student->semesters()
            ->with(['year', 'grades' => fn($q) => $q
                ->with(['course', 'section', 'semester']),])->get();

        $activeSemester = Semester::where('status', 'Active')->with('year')->get();

        if ($activeSemester->isEmpty()) {
            $activeSemester = null;
        } else {
            $activeSemester = SemesterResource::collection($activeSemester)->first()->load('year');
        }

        return Inertia::render('Students/Show', [
            'student' => $student,
            'user' => $user,
            'documents' => UserDocumentResource::collection($user->userDocuments),
            'status' => new StatusResource($student->status),
            'sections' => $sections,
            'studyModes' => $studyModes,
            'courses' => $courses,
            'paymentCategories' => $paymentCategories,
            'paymentMethods' => $paymentMethods,
            'payments' => $payments,
            'semesters' => $semesters,
            'activeSemester' => $activeSemester,
        ]);
    }

    public function create(): Response
    {

        $programs = ProgramResource::collection(Program::with('tracks', 'studyModes')->get());

        $years = YearResource::collection(Year::with('semesters')->orderBy('name')->get());

        $studyModes = StudyModeResource::collection(StudyMode::all());


        return inertia('Students/Create', [
            'programs' => $programs,
            'years' => $years,
            'studyModes' => $studyModes,
        ]);
    }

    // Store the student innformation to the store method in Auth/StudentRegistrationController
    public function store(StudentStoreRequest $request): RedirectResponse
    {
        // Create the student record
        $registerStudent = new StudentRegistrationService;

        $student = $registerStudent->store($request);

        // Load related data for the student resource
        return to_route('students.show', $student);
    }

    public function edit(Student $student): Response
    {
        $programs = ProgramResource::collection(Program::with('studyModes', 'tracks')->get());

        $years = YearResource::collection(Year::with('semesters')->get());
        $student = new StudentResource($student->load('user', 'program', 'track', 'year', 'semester', 'section', 'studyMode', 'status'));
        return Inertia::render('Students/Edit', [
            'student' => $student,
            'programs' => $programs,
            'years' => $years,
        ]);
    }

    public function update(StudentUpdateRequest $request, Student $student): RedirectResponse
    {
        // Update the Student info in the update method in Auth/StudentRegistrationController

        $student = (new StudentRegistrationService)->update($request, $student);

        return to_route('students.show', $student);
    }

    public function destroy(Student $student)
    {
        // Soft delete the student
        $student->status->is_deleted = true;
        $student->status->deleted_at = now();
        $student->status->deleted_by_name = Auth::user()->name;
        $student->status->save();

        // Optionally, you can also delete the associated user record
        $user = $student->user;
        if ($user) {
            $user->is_deleted = true;
            $user->deleted_at = now();
            $user->deleted_by_name = Auth::user()->name;
            $user->save();
        }

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function verify(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $status = $student->status;

        if (! $status) {
            // Create a new status record if it doesn't exist
            $status = new Status;
            $status->student_id = $student->id;
            $status->user_id = $student->user->id; // Assuming you want to set the current user as the one who created the status
        }

        // Define the status fields to be toggled
        if ($request->has('is_active')) {

            if ($status->{'is_active'} == 1) {
                // If it's already 1, set it to 0
                $status->{'is_active'} = 0;
            } else {
                // If it's not set, set it to 1
                $status->{'is_active'} = 1;
            }
        } else {

            $statuses = [
                'approved',
                'completed',
                'verified',
                'enrolled',
                'graduated',
                'scholarship',
                'scholarship_approved',
                'scholarship_verified',
            ];

            // Check if any of the status fields are present in the request
            foreach ($statuses as $statusField) {
                if ($request->has('is_' . $statusField)) {
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
        }
        // Save the status record
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

    // Show student Transcript
    public function transcript(Student $student)
    {
        $student->load([
            'user',
            'program',
            'track',
            'studyMode',
            'year',
            'semester',
            'section',
        ]);

        $semesters = $student->semesters()
            ->with([
                'year',
                'grades' => fn($q) => $q->with(['course', 'section', 'semester']),
            ])
            ->get();

        return Inertia::render('Students/Transcript', [
            'student' => new StudentResource($student),
            'semesters' => $semesters,
        ]);
    }

    /**
     * Register a student to a semester.
     */
    public function registerSemester(Request $request, Student $student)
    {
        $fields = $request->validate([
            'semester_id' => 'required|exists:semesters,id',
        ]);

        // retrieve the section of the student and the year level and semester of the section he/she belongs too
        $section = $student->section;
        $semester = Semester::find($fields['semester_id']);
        $year = $semester->year;

        $semesterLevel = $semester->level;
        $yearLevel = intval($year->name) - intval($section->year->name) + 1;

        // retrieve the courses that student is expected to take in the given year or semester(can still be dropped later if they dont want it)
        $sectionCourseIds = $section->courseSectionAssignments()->where('semester', $semesterLevel)->where('year_level', $yearLevel)->with('course')->get()->pluck('course.id');

        /**
         * Arrange the courses so that it is suitable to sync the student to the courses with section_id pivot column
         * eg: 
         * [
         *  3 (course_id we want to sync) => ['section_id' => 4], so we this student should take this course in the given section
         * ]
         */
        $organizedCourses = [];
        foreach ($sectionCourseIds as $courseId) {
            $organizedCourses[$courseId] = [
                'section_id' => $section->id
            ];
        }

        // sync the courses (when a student registers the courses will be added, but if the students wants to drop all of them...don't worry he can)
        $student->courses()->attach($organizedCourses);

        // Set all previous semester_student records for this student to Inactive
        DB::table('semester_student')
            ->where('student_id', $student->id)
            ->whereIn('status', ['Active', 'Enrolled'])
            ->update(['status' => 'Completed']);


        // Upsert the new/selected semester as Active for this student
        DB::table('semester_student')->updateOrInsert(
            [
                'student_id' => $student->id,
                'semester_id' => $request->semester_id,
            ],
            [
                'status' => 'Enrolled',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Student registered to semester successfully.');
    }

    public function addCourse(Request $request, Student $student)
    {
        $fields = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        // Prevent duplicate entries
        if ($student->courses()->where('course_id', $fields['course_id'])->exists()) {
            return back()->withErrors(['course_id' => 'This course is already assigned to the student.']);
        }

        $student->courses()->attach($fields['course_id'], [
            'status' => 'Enrolled',
            'section_id' => $fields['section_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Course added successfully.');
    }

    public function dropCourse(Request $request, Student $student)
    {
        $fields = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        $student->courses()->detach($fields['course_id']);

        return back()->with('success', 'Course dropped successfully.');
    }
}
