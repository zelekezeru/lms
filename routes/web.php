<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudyModeController;
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
