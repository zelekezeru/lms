<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\Department;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\InstructorStoreRequest;
use App\Http\Requests\InstructorUpdateRequest;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Auth\RegisteredUserController;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $query = Instructor::query();
    
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
    
            // Adjusted for correct column names
            $query->where('specialization', 'LIKE', "%{$search}%")
                  ->orWhere('employment_type', 'LIKE', "%{$search}%");
        }
    
        $instructors = InstructorResource::collection($query->paginate(15)->withQueryString());
    
        return inertia('Instructors/Index', [
            'instructors' => $instructors,
            'search' => $request->search,
        ]);
    }
    
    
    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor)
    {
        $instructor = new InstructorResource($instructor->load('user', 'courses'));

        return Inertia::render('Instructors/Show', [
            'instructor'=> $instructor,
        ]);
    }
    

    public function create(): Response
    {
        $instructors = InstructorResource::collection(Instructor::all());
        $roles = Role::all();

        return Inertia::render('Instructors/Create', [
            'departments' => Department::all(['id', 'name']),
            'roles' => $roles,
        ]);
    }


    public function store(InstructorStoreRequest $request)
    {
        $fields = $request->validated();

        $profileImg = $fields['profile_img'] ?? null;
        
        // Profile   of Instructor
        if ($profileImg) {
            $profile_path = $profileImg->store('profile-images', 'public');
        }else{
            $profile_path = null;
        }

        // Create a new Instructor User in User table
        
        $user_phone = substr($fields['contact_phone'], -4);

        $user_password = 'instructor@' . $user_phone;

        // Merge the default password into the request
        $request->merge([
            'password' => $user_password,
            'default_password' => $user_password, // needed for 'confirmed' rule
            'profile_img' => $profile_path,
        ]);

        $registeredUserController = new RegisteredUserController();

        $user = $registeredUserController->store($request, 'INSTRUCTOR', 'User');
        
        $instructor = Instructor::create([
            'name' => $fields['name'],
            'user_id' => $user->id,
            'tenant_id' => 1,
            'specialization' => $fields['specialization'],
            'employment_type' => $fields['employment_type'],
            'bio' => $fields['bio'],
            'status' => $fields['status'],
            'hire_date' => $fields['hire_date'],
        ]);
        dd($instructor);
        return redirect(route('instructors.show', $instructor))->with('success', 'Instructor created successfully.');
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instructor $instructor)
    {
        $roles = Role::all();

        return inertia('Instructors/Edit', [
            'instructor' => new InstructorResource($instructor->load('user', 'department')),
            'departments' => Department::all(['id', 'name']),
            'roles' => $roles,
        ]);
    }

    public function update(InstructorUpdateRequest $request, Instructor $instructor)
    {
        $fields = $request->validated();
        $profileImg = $fields['profile_img'] ?? null;
        $user = $instructor->user;

        // Handle profile image update
        if ($profileImg) {
            if ($user->profile_img) {
                Storage::disk('public')->delete($user->profile_img);
            }
            $profile_path = $profileImg->store('profile-images', 'public');
        }

        // Update user details
        $user->update([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $profile_path ?? $user->profile_img,
            'phone'=> $fields['contact_phone'],
        ]);

        // Update instructor details
        $instructor->update([
            'specialization' => $fields['specialization'],
            'employment_type' => $fields['employment_type'],
            'hire_date' => $fields['hire_date'],
            'status' => $fields['status'],
            'bio' => $fields['bio'],
        ]);

        // Update roles if provided
        if (!empty($fields['role_name'])) {
            $user->syncRoles([$fields['role_name']]);
        }

        return redirect(route('instructors.show', $instructor))->with('success', 'Instructor updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        $user = $instructor->user;
        if ($user->profile_img) {
            Storage::disk('public')->delete($user->profile_img);
        }
        
        $instructor->delete();

        $user->delete();

        return to_route('instructors.index', $instructor)->with('success', 'Instructor deleted successfully.');  
    }
}
