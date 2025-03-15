<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventorySupplierController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoleController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware('auth')->group(function () {});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Profiles related routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role and Permission Related routes
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('/roles/{role}/permissions', [RoleController::class, 'assign'])->name('roles.permissions');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'attach'])->name('roles.attach');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'detach'])->name('roles.detach');

    // Main Application crud resource routes
    Route::resource('students', StudentController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('inventorySuppliers', InventorySupplierController::class);
    Route::resource('inventoryCategories', InventoryCategoryController::class);
    
    Route::resource('tenants', TenantController::class);
});

require __DIR__ . '/auth.php';
