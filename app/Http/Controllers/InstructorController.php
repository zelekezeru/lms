<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\Department;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\InstructorStoreRequest;
use App\Http\Requests\InstructorUpdateRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

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
        return Inertia::render('Instructors/Show', [
            'instructor' => new InstructorResource($instructor->load('user')) 
        ]);
    }
    

    public function create(): Response
    {
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
        $year = substr(Carbon::now()->year, -2);

        $instructor_id = 'IN/' .  '000/'. User::count()  .  '/' .$fields['name'] .  '/' .$year;  

        $fields['tenant_id'] = 1; // Hardcoded tenant ID for now
        // Profile   of Instructor
        if ($profileImg) {
            $profile_path = $profileImg->store('profile-images', 'public');
        }else{
            $profile_path = null;
        }

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $profile_path,
            'user_uuid' => $instructor_id,
            'password' => Hash::make('pwd@default'),
        ]);

        $instructor = Instructor::create([
            'user_id' => $user->id,
            'specialization' => $fields['specialization'],
            'employment_type' => $fields['employment_type'],
            'bio' => $fields['bio'],
            'hire_date' => $fields['hire_date'],
        ]);
        
        $user->assignRole('INSTRUCTOR');
        
        return redirect(route('instructors.show', $instructor))->with('success', 'Instructor created successfully.');
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instructor $instructor)
    {
        $roles = Role::all();

        return inertia('Instructors/Edit', [
            'instructor' => new InstructorResource($instructor->load('department')),
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

        $user->update([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $profile_path ?? $user->profile_img,
        ]);

        
        $instructor->update([
            'specialization' => $fields['specialization'],
            'employment_type' => $fields['employment_type'],
            'hire_date' => $fields['hire_date'],
            'status' => $fields['status'],
            'bio' => $fields['bio'],
        ]);
       
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
