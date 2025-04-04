<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = UserResource::collection(User::paginate(15));

        return inertia('Users/Index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        
        return inertia('Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validated();

        // Check if the admin is authenticated and has a tenant_id
        


        $userUuid = $this->userUuid('user');

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

        $user = User::create([
            'user_id' => $user->id,
            'job_position' => $fields['job_position'],
            'employment_type' => $fields['employment_type'],
            'office_hours' => $fields['office_hours'],
        ]);
        
        $user->assignRole($fields['role_name']);
        
        return redirect(route('users.show', $user));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return inertia('Users/Show', [
            'user' => new UserResource($user->load('user')),
        ]);
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return inertia('Users/Edit', [
            'user' => new UserResource($user->load('user')),
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $fields = $request->validated();
        $image = $fields['profile_img'] ?? null;
        $user = $user->user;
        
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

        $user->update([
            'job_position' => $fields['job_position'],
            'employment_type' => $fields['employment_type'],
            'office_hours' => $fields['office_hours'],
        ]);
        
        if (!empty($fields['role_name'])) {
            $user->syncRoles([$fields['role_name']]);
        }

        return redirect(route('users.show', $user));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user = $user->user;
        if ($user->profile_img) {
            Storage::disk('public')->delete($user->profile_img);
        }
        
        $user->delete();
        $user->delete();
        return to_route('users.index');
    }

    public function userUuid($role)
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two digits

        $tenant = substr(Tenant::first()->name, -1); // get the first tenant name

        $userUuid = $tenant .  '/EM/' . str_pad(User::where()->count() + 1, 3, '0', STR_PAD_LEFT) . '/' . $year;

        return $userUuid;
    }
}
