<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProgramController;
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

    // CRUD Resources with Permissions
    // $controllers = [
    //     'students' => StudentController::class,
    //     'departments' => DepartmentController::class,
    //     'programs' => ProgramController::class,
    //     'employees' => EmployeeController::class,
    //     'inventories' => InventoryController::class,
    //     'inventorySuppliers' => InventorySupplierController::class,
    //     'inventoryCategories' => InventoryCategoryController::class,
    //     'instructors' => InstructorController::class,
    //     'academic-documents' => AcademicDocumentController::class,
    //     'tenants' => TenantController::class,
    // ];

    $resourceRoutes = [
        'departments' => 'Department',
        'students' => 'Student',
        'programs' => 'Program',
        'employees' => 'Employee',
        'inventories' => 'Inventory',
        'inventorySuppliers' => 'InventorySupplier',
        'inventoryCategories' => 'InventoryCategory',
        'instructors' => 'Instructor',
        'academic-documents' => 'AcademicDocument',
        'tenants' => 'Tenant',
    ];

    foreach ($resourceRoutes as $route => $singular) {
        $controller = "App\\Http\\Controllers\\{$singular}Controller";
        $singularToLower = strtolower($singular);

        Route::middleware("can:create-$route")->post("$route", [$controller, 'store'])->name("$route.store");
        Route::middleware("can:create-$route")->get("$route/create", [$controller, 'create'])->name("$route.create");
        
        Route::middleware("can:view-$route")->get("$route", [$controller, 'index'])->name("$route.index");
        Route::middleware("can:view-$route")->get("$route/{{$singularToLower}}", [$controller, 'show'])->name("$route.show");
        
        Route::middleware("can:update-$route")->put("$route/{{$singularToLower}}", [$controller, 'update'])->name("$route.update");
        Route::middleware("can:update-$route")->get("$route/{{$singularToLower}}/edit", [$controller, 'edit'])->name("$route.edit");

        Route::middleware("can:delete-$route")->delete("$route/{{$singularToLower}}", [$controller, 'destroy'])->name("$route.destroy");
    }

    Route::resource('tenants', TenantController::class)->except(['create', 'edit']);
    Route::resource('programs', ProgramController::class)->except(['create', 'edit']);
});


require __DIR__ . '/auth.php';
