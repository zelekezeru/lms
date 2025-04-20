<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\EmployeeResource;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Auth\RegisteredUserController;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $employees = Employee::query()
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('job_position', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $search]);
    
        return inertia('Employees/Index', [
            'employees' => EmployeeResource::collection($employees),
            'search' => $search,
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
        dd($fields);

        $image = $fields['profile_img'] ?? null;
        
        $registeredUserController = new RegisteredUserController();

        $admin = \Illuminate\Support\Facades\Auth::user();

        // Check if the admin is authenticated and has a tenant_id
        if (!$admin || !property_exists($admin, 'tenant_id')) {
            abort(403, 'Unauthorized action or missing tenant_id.');
        }else{
            $tenant_id = $admin->tenant_id;
        }


        $userUuid = $registeredUserController->userUuid('EMPLOYEE', 'User', $tenant = Tenant::first());

        dd('HIT');

        // $userUuid = $this->userUuid($fields['role_name']);
        // $year = substr(Carbon::now()->year, -2); // get current year's last two digits

        // $instructor_id = 'EM/' .  '000/'. User::count()  .  '/' .$fields['name'] .  '/' .$year;  

        
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
        
        return redirect(route('employees.show', $employee))->with('success', 'Employee created successfully.');
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

        // Handle profile image upload
        if ($image) {
            if ($user->profile_img) {
                Storage::disk('public')->delete($user->profile_img);
            }
            $profile_path = $image->store('profile-images', 'public');
        }

        // Update user details
        $user->update([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $profile_path ?? $user->profile_img,
        ]);

        // Update employee details
        $employee->update([
            'job_position' => $fields['job_position'],
            'employment_type' => $fields['employment_type'],
            'office_hours' => $fields['office_hours'],
        ]);

        // Update roles if provided
        if (!empty($fields['role_name'])) {
            $user->syncRoles([$fields['role_name']]);
        }

        return redirect(route('employees.show', $employee))->with('success', 'Employee updated successfully.');
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

        $user->delete();
        
        return to_route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function userUuid($role)
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two digits

        $tenant = substr(Tenant::first()->name, -1); // get the first tenant name

        $userUuid = $tenant .  '/EM/' . str_pad(Employee::where()->count() + 1, 3, '0', STR_PAD_LEFT) . '/' . $year;

        return $userUuid;
    }
}
