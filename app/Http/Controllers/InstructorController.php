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
    public function index(): Response
    {
        $instructors = InstructorResource::collection(Instructor::with('department')->paginate(15));

        return inertia('Instructors/Index', [
            'instructors' => $instructors
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor)
    {

        return inertia('Instructors/Show', [
            'instructor' => new InstructorResource($instructor->load('department'))
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
        
        $year = substr(Carbon::now()->year, -2);

        $instructor_id = 'IN/' .  '000/'. User::count()  .  '/' .$fields['name'] .  '/' .$year;  

        $fields['tenant_id'] = 1; // Hardcoded tenant ID for now
        
        // Profile   of Institution
        if ($request->hasFile('profile_img')) {
            $fields['profile_img'] = $request->file('profile_img')->store('instructors', 'public');

            //Add storage directory to Image Name
            $fields['profile_img'] = '/storage/' . $fields['profile_img'];
        }else{
            $fields['profile_img'] = null;
            
        }
        
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $fields['profile_img'],
            'user_uuid' => $instructor_id,
            'password' => Hash::make('pwd@default'),
        ]);

        $instructor = Instructor::create([
            'department_id' => $fields['department_id'],
            'user_id' => $user->id,
            'specialization' => $fields['specialization'],
            'employment_type' => $fields['employment_type'],
            'bio' => $fields['bio'],
            'hire_date' => $fields['hire_date'],
        ]);
        
        $user->assignRole($fields['role_name']);
        
        return redirect(route('instructors.index', $instructor));
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
        $data = $request->validated();

        // Handle profile image update
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($instructor->profile_image) {
                Storage::disk('public')->delete($instructor->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('instructors', 'public');
        }

        $instructor->update($data);

        return redirect()->route('instructors.index')->with('success', 'Instructor updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        return redirect()->route('instructors.index')->with('success', 'Instructor deleted successfully.');
    }
}
