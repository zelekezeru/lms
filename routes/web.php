<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudyModeController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventorySupplierController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\UserDocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\CurriculumCourseController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;




Route::middleware('auth')->group(function () {});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', fn() => Inertia::render('Dashboard'))->name('dashboard');

    // Profiles related routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

    Route::post('/instructor-courseSection/{section}/{course}', [AssignmentController::class, 'assignInstructorToCourseSection'])->name('instructor-courseSection.assign');

    Route::post('/students-section', [AssignmentController::class, 'assignStudentsToSection'])->name('students-section.assign');

    Route::post('/courses-student/{student}', [AssignmentController::class, 'assignCoursesToStudents'])->name('courses-student.assign');

    //Student Managment
    Route::get('/students/{student}/profile', [ProfileController::class, 'profile'])->name('students.profile');
    Route::post('/students/{student}/updateProfile', [ProfileController::class, 'updateProfile'])->name('students.updateProfile');
    Route::post('/students/{student}/verify', [StudentController::class, 'verify'])->name('students.verify');

    // Assessment routes
    Route::get('/assessments/section_course/{section}/{course}', [AssessmentController::class, 'section_course'])->name('assessments.section_course');
    Route::get('/assessments/section_student/{section}/{student}', [AssessmentController::class, 'section_student'])->name('assessments.section_student');
        
    // Curriculum routes
    Route::get('curricula/{curriculum}/assign-courses', [CurriculumCourseController::class, 'edit'])->name('curricula.assign');
    Route::post('curricula/{curriculum}/assign-courses', [CurriculumCourseController::class, 'update'])->name('curricula.assign.update');

    Route::get('/instructor', function () {
        return Inertia::render('Instructor');
    })->name('instructor.dashboard');
    
        // INstructor related routes
    Route::get('/instructor/course', function () {
        return Inertia::render('Instructor/InstructorCourses');
    })->name('instructor.course');
    

    Route::get('/instructor/attendance', function () {
        return Inertia::render('Instructor/InstructorAttendance');
    })->name('instructor.attendance');
    

    Route::get('/instructor/grades', function () {
        return Inertia::render('Instructor/InstructorGrades');
    })->name('instructor.grades');
    

    Route::get('/instructor/schedule', function () {
        return Inertia::render('Instructor/Schedule');
    })->name('instructor.schedule');
    

    Route::get('/student', function () {
        return Inertia::render('Student');
    })->name('student.dashboard');
    

    Route::get('/payment', function () {
        return Inertia::render('Student/Payment');
    })->name('student.payment');
    

    Route::get('/student/registration', function () {
        return Inertia::render('Student/Registration');
    })->name('student.registration');
    

    Route::get('/student/add', function () {
        return Inertia::render('Student/AddDropCourses');
    })->name('student.add');
    
    
    Route::get('/student/course', function () {
        return Inertia::render('Student/Course');
    })->name('student.course');
    
    
    Route::get('/student/result', function () {
        return Inertia::render('Student/StudentResults');
    })->name('student.result');
    
    $resourceRoutes = [
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
        'sections' => 'section',
        'results' => 'result',
        'weights' => 'weight',
        'grades' => 'grade',
        'curriculums' => 'curricula',
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
});


require __DIR__ . '/auth.php';
