<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

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

        $profileImg = $fields['profile_img'] ?? null;

        // Profile   of Instructor
        if ($profileImg) {
            $profile_path = $profileImg->store('profile-images', 'public');
        } else {
            $profile_path = null;
        }

        // user default password
        $firstName = explode(' ', $fields['name'])[0]; // Get the first name from the full name
        
        $user_phone = substr($fields['contact_phone'], -4);

        $default_password = strtolower($firstName).'@'.$user_phone; // Default password for new users

        // Merge the default password into the request
        $request->merge([
            'password' => $default_password,
            'default_password' => $default_password, // needed for 'confirmed' rule
            'profile_img' => $profile_path,
        ]);

        $employee = Employee::create([
            // User id temporary
            'user_id' => 3,
            'job_position' => $fields['job_position'],
            'employment_type' => $fields['employment_type'],
            'office_hours' => $fields['office_hours'],
        ]);

        // Create a new User
        $registeredUserController = new RegisteredUserController;

        $user = $registeredUserController->store($request, $request->role_name, 'Employee', $employee);

        return redirect(route('employees.show', $employee))->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $user = new UserResource($employee->user->load('userDocuments'));

        $documents = $user->userDocuments;

        return inertia('Employees/Show', [
            'employee' => new EmployeeResource($employee->load('user')),
            'user' => $user,
            'documents' => $documents,
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
        if (! empty($fields['role_name'])) {
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

        $userUuid = $tenant.'/EM/'.str_pad(Employee::where()->count() + 1, 3, '0', STR_PAD_LEFT).'/'.$year;

        return $userUuid;
    }
}
