<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoleController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProgramController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('students', StudentController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('programs', ProgramController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    
    Route::get('/roles/{role}/permissions', [RoleController::class, 'assign'])->name('roles.permissions');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'attach'])->name('roles.attach');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'detach'])->name('roles.detach');       
    
});

Route::middleware(['auth'])->group(function () {
    Route::resource('permissions', PermissionController::class);
});

require __DIR__.'/auth.php';
