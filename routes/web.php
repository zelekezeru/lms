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
    Route::get('/assignments/courses-sections', [AssignmentController::class, 'create_courses_sections'])->name('assignments.courses-sections');
    Route::post('/assignments/courses-sections', [AssignmentController::class, 'assign_courses_sections'])->name('assignments.courses-sections.assign');
    Route::post('/assignments/courses-sections/remove', [AssignmentController::class, 'remove_courses_sections'])->name('assignments.courses-sections.remove');
    
    Route::get('/assignments/instructors-sections', [AssignmentController::class, 'create_instructors_sections'])->name('assignments.instructors-sections');
    Route::post('/assignments/instructors-sections', [AssignmentController::class, 'assign_instructors_sections'])->name('assignments.instructors-sections.assign');
    Route::post('/assignments/instructors-sections/remove', [AssignmentController::class, 'remove_instructors_sections'])->name('assignments.instructors-sections.remove');
    
    Route::get('/assignments/instructors-courses', [AssignmentController::class, 'create_instructors_courses'])->name('assignments.instructors-courses');
    Route::post('/assignments/instructors-courses', [AssignmentController::class, 'assign_instructors_courses'])->name('assignments.instructors-courses.assign');
    Route::post('/assignments/instructors-courses/remove', [AssignmentController::class, 'remove_instructors_courses'])->name('assignments.instructors-courses.remove');
    
    Route::get('/assignments/students-sections', [AssignmentController::class, 'create_students_sections'])->name('assignments.students-sections');
    Route::post('/assignments/students-sections', [AssignmentController::class, 'assign_students_sections'])->name('assignments.students-sections.assign');
    Route::post('/assignments/students-sections/remove', [AssignmentController::class, 'remove_students_sections'])->name('assignments.students-sections.remove');
    
    Route::get('/assignments/students-courses', [AssignmentController::class, 'create_students_courses'])->name('assignments.students-courses');
    Route::post('/assignments/students-courses', [AssignmentController::class, 'assign_students_courses'])->name('assignments.students-courses.assign');
    Route::post('/assignments/students-courses/remove', [AssignmentController::class, 'remove_students_courses'])->name('assignments.students-courses.remove');

    //Student Managment
    Route::get('/students/{student}/profile', [StudentController::class, 'profile'])->name('students.profile');
    Route::post('/students/{student}/updateProfile', [StudentController::class, 'updateProfile'])->name('students.updateProfile');

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
