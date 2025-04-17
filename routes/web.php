<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudyModeController;
use App\Http\Controllers\DepartmentController;
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
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;




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
    // Attaching Section to courses, instructors, and students
    Route::get('/programs/{program}/courses', [AssignmentController::class, 'program_courses'])->name('program.courses');
    Route::post('/programs/{program}/courses', [AssignmentController::class, 'attach_program_courses'])->name('program-courses.attach');
    Route::post('/programs/{program}/courses/{course}', [AssignmentController::class, 'detach_program_courses'])->name('program-courses.detach');

    Route::get('/departments/{department}/courses', [AssignmentController::class, 'department_courses'])->name('department.courses');
    Route::post('/departments/{department}/courses', [AssignmentController::class, 'attach_department_courses'])->name('department-courses.attach');
    Route::post('/departments/{department}/courses/{course}', [AssignmentController::class, 'detach_department_courses'])->name('department-courses.detach');
    
    Route::get('/sections/{section}/instructors', [AssignmentController::class, 'section_instructors'])->middleware('can:assign-section-instructors')->name('section.instructors');
    Route::post('/sections/{section}/instructors', [AssignmentController::class, 'attach_section_instructors'])->middleware('can:attach-section-instructors')->name('section-instructors.attach');
    Route::post('/sections/{section}/instructors/{instructor}', [AssignmentController::class, 'detach_section_instructors'])->middleware('can:detach-section-instructors')->name('section-instructors.detach');

    Route::get('/sections/{section}/students', [AssignmentController::class, 'section_students'])->middleware('can:assign-section-students')->name('section.students');
    Route::post('/sections/{section}/students', [AssignmentController::class, 'attach_section_students'])->middleware('can:attach-section-students')->name('section-students.attach');
    Route::post('/sections/{section}/students/{student}', [AssignmentController::class, 'detach_section_students'])->middleware('can:detach-section-students')->name('section-students.detach');

    // Attaching Course to instructors and students
    Route::get('/courses/{course}/instructors', [AssignmentController::class, 'course_instructors'])->name('course.instructors');
    Route::post('/courses/{course}/instructors', [AssignmentController::class, 'attach_course_instructors'])->name('course-instructors.attach');
    Route::post('/courses/{course}/instructors/{instructor}', [AssignmentController::class, 'detach_course_instructors'])->name('course-instructors.detach');

    Route::get('/courses/{course}/students', [AssignmentController::class, 'course_students'])->name('course.students');
    Route::post('/courses/{course}/students', [AssignmentController::class, 'attach_course_students'])->name('course-students.attach');
    Route::post('/courses/{course}/students/{student}', [AssignmentController::class, 'detach_course_students'])->name('course-students.detach');

        //Student Managment
    Route::get('/students/{student}/profile', [ProfileController::class, 'profile'])->name('students.profile');
    Route::post('/students/{student}/updateProfile', [ProfileController::class, 'updateProfile'])->name('students.updateProfile');

    $resourceRoutes = [
        'departments' => 'department',
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
    ];

    foreach ($resourceRoutes as $route => $singular) {
        $singularCapitalized = ucfirst($singular);
        $controller = "App\\Http\\Controllers\\{$singularCapitalized}Controller";

        Route::middleware("can:create-$route")->post("$route", [$controller, 'store'])->name("$route.store");
        Route::middleware("can:create-$route")->get("$route/create", [$controller, 'create'])->name("$route.create");
        
        Route::middleware("can:view-$route")->get("$route", [$controller, 'index'])->name("$route.index");
        Route::middleware("can:view-$route")->get("$route/{{$singular}}", [$controller, 'show'])->name("$route.show");
        
        Route::middleware("can:update-$route")->put("$route/{{$singular}}", [$controller, 'update'])->name("$route.update");
        Route::middleware("can:update-$route")->patch("$route/{{$singular}}", [$controller, 'update'])->name("$route.update");
        Route::middleware("can:update-$route")->get("$route/{{$singular}}/edit", [$controller, 'edit'])->name("$route.edit");

        Route::middleware("can:delete-$route")->delete("$route/{{$singular}}", [$controller, 'destroy'])->name("$route.destroy");
    }

});


require __DIR__ . '/auth.php';
