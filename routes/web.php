<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinancePortalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InstructorPortalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrarPortalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\StudyModeController;
use App\Http\Controllers\UserDocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {});

Route::middleware(['auth'])->group(function () {
    // Student Dashboard
    Route::get('/', function () {
        $user = Auth::user();

        if (!$user->loggedInAs) {
            $user->active_role_id = $user->roles->first()?->id;
            $user->save();
        }

        $roleName = $user->loggedInAs ? strtoupper($user->loggedInAs->name) : null;

        if ($roleName === 'STUDENT') {
            return redirect()->route('student.dashboard');
        } elseif ($roleName === 'INSTRUCTOR') {
            return redirect()->route('instructor.dashboard');
        } elseif ($roleName === 'REGISTRAR') {
            return redirect()->route('registrar.dashboard');
        } elseif ($roleName === 'FINANCE-ADMIN' || $roleName === 'FINANCE-USER') {
            return redirect()->route('finance.dashboard');
        } else {
            return redirect()->route('admin.dashboard');
        }
    })->name('dashboard');
    // Admin Dashboard
    Route::get('/admin-dashboard', [HomeController::class, 'index'])->name('admin.dashboard');

    // Student Portal
    Route::group(['prefix' => 'st-portal', 'middleware' => ['role:STUDENT']], function () {
        Route::get('/', [StudentPortalController::class, 'index'])->name('student.dashboard');
        Route::get('/enrollments', [StudentPortalController::class, 'enrollments'])->name('student.enrollments');
        Route::get('/enrollments/{enrollment}', [StudentPortalController::class, 'enrollmentDetail'])->name('student.enrollments.show');
        Route::get('/classSchedules', [StudentPortalController::class, 'classSchedules'])->name('student.classSchedules');
        Route::get('/classSessions', [StudentPortalController::class, 'classSessions'])->name('student.classSessions');
        Route::get('/profile', [StudentPortalController::class, 'profile'])->name('student.profile');
        Route::get('/result', [StudentPortalController::class, 'result'])->name('student.result');
        Route::get('/payments', [StudentPortalController::class, 'payment'])->name('student.payment');
    });

    // Instructor Portal
    Route::group(['prefix' => 'in-portal', 'middleware' => ['role:INSTRUCTOR']], function () {
        Route::get('/', [InstructorPortalController::class, 'index'])->name('instructor.dashboard');
        Route::get('/courses', [InstructorPortalController::class, 'courses'])->name('instructor.courses');
        Route::get('/courses/{course}', [InstructorPortalController::class, 'courseDetail'])->name('instructor.courses.detail');
        Route::get('/sections', [InstructorPortalController::class, 'sections'])->name('instructor.sections');
        Route::get('/sections/{section}', [InstructorPortalController::class, 'sectionDetail'])->name('instructor.sections.detail');

        Route::get('sections/{section}/courses/{course}/students', [InstructorPortalController::class, 'sectionCourseStudents'])->name('instructor.sections.courses.students');
        Route::get('sections/{section}/courses/{course}/assessments', [InstructorPortalController::class, 'sectionCourseAssessments'])->name('instructor.sections.courses.assessments');
        Route::get('sections/{section}/courses/{course}/attendance', [InstructorPortalController::class, 'sectionCourseAttendance'])->name('instructor.sections.courses.attendance');
        Route::get('sections/{section}/courses/{course}', [InstructorPortalController::class, 'sectionCourse'])->name('instructor.sections.courses');

        Route::get('/result', [InstructorPortalController::class, 'result'])->name('instructor.result');
        Route::get('/calendars', [InstructorPortalController::class, 'calendar'])->name('instructor.calendar');
        Route::get('/profile', [InstructorPortalController::class, 'profile'])->name('instructor.profile');
        Route::get('/classSchedules', [InstructorPortalController::class, 'classSchedules'])->name('instructor.classSchedules');
        Route::get('/classSessions', [InstructorPortalController::class, 'classSessions'])->name('instructor.classSessions');
    });
    // Instructor Portal
    Route::group(['prefix' => 'reg-portal', 'middleware' => ['role:REGISTRAR']], function () {
        Route::get('/', [RegistrarPortalController::class, 'index'])->name('registrar.dashboard');
        // Route::get('/courses', [RegistrarPortalController::class, 'courses'])->name('instructor.courses');
        // Route::get('/courses/{course}', [RegistrarPortalController::class, 'courseDetail'])->name('instructor.courses.detail');
        // Route::get('/sections', [RegistrarPortalController::class, 'sections'])->name('instructor.sections');
        // Route::get('/sections/{section}', [RegistrarPortalController::class, 'sectionDetail'])->name('instructor.sections.detail');

        Route::get('students', [RegistrarPortalController::class, 'sectionCourseStudents'])->name('registrar.students');
        // Route::get('sections/{section}/courses/{course}/assessments', [RegistrarPortalController::class, 'sectionCourseAssessments'])->name('instructor.sections.courses.assessments');
        // Route::get('sections/{section}/courses/{course}/attendance', [RegistrarPortalController::class, 'sectionCourseAttendance'])->name('instructor.sections.courses.attendance');
        // Route::get('sections/{section}/courses/{course}', [RegistrarPortalController::class, 'sectionCourse'])->name('instructor.sections.courses');

        // Route::get('/result', [RegistrarPortalController::class, 'result'])->name('instructor.result');
        // Route::get('/calendars', [RegistrarPortalController::class, 'calendar'])->name('instructor.calendar');
        // Route::get('/profile', [RegistrarPortalController::class, 'profile'])->name('instructor.profile');
        // Route::get('/classSchedules', [RegistrarPortalController::class, 'classSchedules'])->name('instructor.classSchedules');
    });

    Route::group(['prefix' => 'fin-portal', 'middleware' => ['role:FINANCE-ADMIN|FINANCE-USER']], function () {
        Route::get('/', [FinancePortalController::class, 'index'])->name('finance.dashboard');
        // Route::get('/courses', [FinancePortalController::class, 'courses'])->name('instructor.courses');
        // Route::get('/courses/{course}', [FinancePortalController::class, 'courseDetail'])->name('instructor.courses.detail');
        // Route::get('/sections', [FinancePortalController::class, 'sections'])->name('instructor.sections');
        // Route::get('/sections/{section}', [FinancePortalController::class, 'sectionDetail'])->name('instructor.sections.detail');

        Route::get('students', [FinancePortalController::class, 'sectionCourseStudents'])->name('finance.students');
        // Route::get('sections/{section}/courses/{course}/assessments', [FinancePortalController::class, 'sectionCourseAssessments'])->name('instructor.sections.courses.assessments');
        // Route::get('sections/{section}/courses/{course}/attendance', [FinancePortalController::class, 'sectionCourseAttendance'])->name('instructor.sections.courses.attendance');
        // Route::get('sections/{section}/courses/{course}', [FinancePortalController::class, 'sectionCourse'])->name('instructor.sections.courses');

        // Route::get('/result', [FinancePortalController::class, 'result'])->name('instructor.result');
        // Route::get('/calendars', [FinancePortalController::class, 'calendar'])->name('instructor.calendar');
        // Route::get('/profile', [FinancePortalController::class, 'profile'])->name('instructor.profile');
        // Route::get('/classSchedules', [FinancePortalController::class, 'classSchedules'])->name('instructor.classSchedules');
    });


    // Profiles related routes
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('switch-role', [AuthenticatedSessionController::class, 'switchRole'])->name('switch-role');

    // User Ducuments
    Route::get('/newDocument/{user_id}', [UserDocumentController::class, 'newDocument'])->name('userDocuments.newDocument');

    // Semester Managment
    Route::prefix('semesters')->name('semesters.')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('index');
        Route::get('/active', [CalendarController::class, 'showActive'])->name('showActive');
        Route::get('/close', [CalendarController::class, 'closeSemesterForm'])->name('closeForm');
        Route::post('/close', [CalendarController::class, 'closeSemester'])->name('close');
        Route::get('/{semester}', [CalendarController::class, 'show'])->name('show');
    });

    // Student Semester Registration
    Route::post('/students/{student}/registerSemester', [StudentController::class, 'registerSemester'])->name('students.registerSemester');

    // User Profile Picture Update
    Route::post('/users/{user}/update-image', [UserController::class, 'updateProfilePicture'])->name('users.update.image');
    
    Route::post('/students/{student}/payment-code', [StudentController::class, 'generatePaymentCode'])->name('students.payment-code');

    // Excel Operation Routes
    Route::get('/sections/{section}/students/export', [ExportController::class, 'exportSectionStudents'])->name('sectionStudents.export');
    Route::get('/instructors/export/{role}', [ExportController::class, 'exportUsers'])->name('instructors.export');
    Route::get('/centers/{center}/students/export', [ExportController::class, 'exportCenterStudents'])->name('centerStudents.export');

    Route::post('/section-students/import', [ImportController::class, 'sectionStudents'])->name('sectionStudents.import');
    Route::post('/center-students/import', [ImportController::class, 'centerStudents'])->name('centerStudents.import');
    Route::post('/section-grades/import', [ImportController::class, 'gradesImport'])->name('sectionGrades.import');

    // Role and Permission Routes
    Route::middleware(['can:view-roles'])->resource('roles', RoleController::class);
    Route::middleware(['can:view-permissions'])->resource('permissions', PermissionController::class);

    Route::get('/roles/{role}/permissions', [RoleController::class, 'assign'])->middleware('can:assign-permissions-roles')->name('roles.permissions');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'attach'])->middleware('can:attach-permissions-roles')->name('roles.attach');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'detach'])->middleware('can:detach-permissions-roles')->name('roles.detach');

    // Assignment routes
    Route::post('/courses-track/{track}', [AssignmentController::class, 'assignCoursesToTracks'])->name('courses-track.assign');
    Route::post('/courses-section/{section}', [AssignmentController::class, 'assignCoursesToSections'])->name('courses-section.assign');
    Route::post('/courses-instructor/{instructor}', [AssignmentController::class, 'assignCoursesToInstructor'])->name('courses-instructor.assign');
    Route::post('/instructors-course/{course}', [AssignmentController::class, 'assignInstructorsToCourse'])->name('instructors-course.assign');
    Route::post('/instructor-courseSection/{section}/{course}', [AssignmentController::class, 'assignInstructorToCourseSection'])->name('instructor-courseSection.assign');
    Route::post('/students-section', [AssignmentController::class, 'assignStudentsToSection'])->name('students-section.assign');
    Route::post('/studyMode-program', [AssignmentController::class, 'assignStudyModeToProgram'])->name('studyMode-program.assign');
    Route::post('/update-section-course/{section}', [AssignmentController::class, 'updateSectionCourse'])->name('update-section-course');    // For One Student To One Section Assignement
    Route::post('/student-section', [AssignmentController::class, 'assignStudentToSection'])->name('student-section.assign');

    // Detach Program from Study Mode
    Route::delete('/studymodes/{study_mode}/programs/{program}', [StudyModeController::class, 'destroyProgram'])->name('studymodes.programs.destroy');

    Route::post('/enrollment-student/{student}/add', [StudentController::class, 'addEnrollment'])->name('enrollments-student.add');
    Route::post('/enrollment-student/{student}/drop', [StudentController::class, 'dropEnrollment'])->name('enrollments-student.drop');

    // Student Managment
    Route::get('/students/{student}/profile', [ProfileController::class, 'profile'])->name('students.profile');
    Route::post('/students/{student}/updateProfile', [ProfileController::class, 'updateProfile'])->name('students.updateProfile');
    Route::post('/students/{student}/verify', [StudentController::class, 'verify'])->name('students.verify');
    Route::get('/students/{student}/transcript', [StudentController::class, 'transcript'])->name('students.transcript');

    // Student Scholarship 
    Route::post('/students/{student}/request-scholarship', [StatusController::class, 'requestScholarship'])->name('students.request-scholarship');
    Route::post('/students/{student}/approve-scholarship', [StatusController::class, 'approveScholarship'])->name('students.approve-scholarship');
    Route::post('/students/{student}/reject-scholarship', [StatusController::class, 'rejectScholarship'])->name('students.reject-scholarship');

    // Assessment routes
    Route::get('/assessments/section_course/{section}/{course}', [AssessmentController::class, 'section_course'])->name('assessments.section_course');
    Route::get('/assessments/section_student/{section}/{student}', [AssessmentController::class, 'section_student'])->name('assessments.section_student');

    // Resource Routes
    $resourceRoutes = [
        'rooms' => 'room',
        'tracks' => 'track',
        'students' => 'student',
        'programs' => 'program',
        'courses' => 'course',
        'employees' => 'employee',
        'inventories' => 'inventory',
        'inventorySuppliers' => 'inventorySupplier',
        'inventoryCategories' => 'inventoryCategory',
        'instructors' => 'instructor',
        'userDocuments' => 'userDocument',
        'tenants' => 'tenant',
        'studyModes' => 'studyMode',
        'users' => 'user',
        'years' => 'year',
        'semesters' => 'semester',
        'calendars' => 'calendar',
        'classSchedules' => 'classSchedule',
        'classSessions' => 'classSession',
        'attendances' => 'attendance',
        'sections' => 'section',
        'results' => 'result',
        'weights' => 'weight',
        'grades' => 'grade',
        'payments' => 'payment',
        'paymentItems' => 'paymentItem',
        'paymentCategories' => 'paymentCategory',
        'paymentSchedules' => 'paymentSchedule',
        'paymentTypes' => 'paymentType',
        'paymentMethods' => 'paymentMethod',
        'centers' => 'center',
        'coordinators' => 'coordinator',
    ];

    foreach ($resourceRoutes as $route => $singular) {
        $singularCapitalized = ucfirst($singular);
        $controller = "App\\Http\\Controllers\\{$singularCapitalized}Controller";

        Route::middleware("can:create-$route")->post("$route", [$controller, 'store'])->name("$route.store");
        Route::middleware("can:create-$route")->get("$route/create", [$controller, 'create'])->name("$route.create");

        Route::middleware("can:view-$route")->get("$route", [$controller, 'index'])->name("$route.index");
        Route::middleware("can:view-$route")->get("$route/{{$singular}}", [$controller, 'show'])->name("$route.show");

        Route::middleware("can:update-$route")
            ->match(['put', 'patch'], "$route/{{$singular}}", [$controller, 'update'])
            ->name("$route.update");

        Route::middleware("can:update-$route")->get("$route/{{$singular}}/edit", [$controller, 'edit'])->name("$route.edit");

        Route::middleware("can:delete-$route")->delete("$route/{{$singular}}", [$controller, 'destroy'])->name("$route.destroy");
    }
    Route::resource('curricula', CurriculumController::class);
});

require __DIR__ . '/auth.php';
