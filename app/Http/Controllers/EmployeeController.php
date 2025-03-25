<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\EmployeeResource;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = EmployeeResource::collection(Employee::with('department')->paginate(15));

        return inertia('Employees/Index', [
            'employees' => $employees
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        
        return inertia('Employees/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeStoreRequest $request)
    {
        $fields = $request->validated();
        $image = $fields['profile_img'] ?? null;
        $userUuid = $this->userUuid($fields['role_name']);
        
        if ($image) {
            $profile_path = $image->store('profile-images', 'public');
        }else{
            $profile_path = null;
        }
        
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $profile_path,
            'user_uuid' => $userUuid,
            'password' => Hash::make('pwd@default'),
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'job_position' => $fields['job_position'],
            'employment_type' => $fields['employment_type'],
            'office_hours' => $fields['office_hours'],
        ]);
        
        $user->assignRole($fields['role_name']);
        
        return redirect(route('employees.show', $employee));
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return inertia('Employees/Show', [
            'employee' => new EmployeeResource($employee->load('user')),
        ]);
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $roles = Role::all();

        return inertia('Employees/Edit', [
            'employee' => new EmployeeResource($employee->load('user')),
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeUpdateRequest $request, Employee $employee)
    {
        $fields = $request->validated();
        $image = $fields['profile_img'] ?? null;
        $user = $employee->user;
        if ($image) {
            if ($user->profile_img) {
                Storage::disk('public')->delete($user->profile_img);
            }
            $profile_path = $image->store('profile-images', 'public');
        }
        
        $user->update([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $profile_path ?? $user->profile_img,
        ]);

        $employee->update([
            'job_position' => $fields['job_position'],
            'employment_type' => $fields['employment_type'],
            'office_hours' => $fields['office_hours'],
        ]);
        
        if (!empty($fields['role_name'])) {
            $user->syncRoles([$fields['role_name']]);
        }

        return redirect(route('employees.show', $employee));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $user = $employee->user;
        if ($user->profile_img) {
            Storage::disk('public')->delete($user->profile_img);
        }

        $employee->delete();
    }

    public function userUuid($role)
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two digits

        $userUuid = substr($role, 0, 3) . '/' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT) . '/' . $year;

        return $userUuid;
    }
}
