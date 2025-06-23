<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Instructor;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = UserResource::collection(User::orderBy('name')->paginate(30));

        return inertia('Users/Index', [
            'users' => $users,
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
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user = new UserResource($user->load('tenant', 'roles'));

        return inertia('Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $fields = $request->validated();

        $registeredUserController = new RegisteredUserController;

        $user = $registeredUserController->store($request, 'USER', 'User');

        $image = $request->file('profile_img');

        if ($image) {
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = storage_path('app/public/profile-images/' . $imageName);

            Image::make($image)->resize(300, 300)->save($path);

            $user->update([
                'profile_img' => 'profile-images/' . $imageName,
            ]);
        }

        $user->assignRole($fields['role_name']);

        return redirect()->route('users.show', $user)->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('tenant', 'roles');

        return inertia('Users/Edit', [
            'user' => new UserResource($user),
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $fields = $request->validated();

        $user->syncRoles($fields['roles']);
        $userId = $user->id;

        cache()->forget("user_roles_{$userId}");
        cache()->forget("user_permissions_{$userId}");

        $roles = cache()->remember(
            "user_roles_{$userId}",
            now()->addMinutes(10),
            fn() => $user->getRoleNames()
        );

        $permissions = cache()->remember(
            "user_permissions_{$userId}",
            now()->addMinutes(10),
            fn() => $user->getAllPermissions()->pluck('name')
        );

        // if user and doesnt have an instructor instance create it
        if ($user->hasRole('INSTRUCTOR') && ! $user->instructor) {
            $instructor = Instructor::create([
                'name' => $fields['name'],
                'user_id' => $user->id,
                'specialization' => "Instructor",
                'employment_type' => "FULL-TIME",
                'bio' => "This is an instructor",
                'status' => 'Active',
                'hire_date' => Carbon::now(),
            ]);
        }

        // Update user details
        $user->update([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $profile_path ?? $user->profile_img,
        ]);

        // Update roles if provided
        if (! empty($fields['role_name'])) {
            $user->syncRoles([$fields['role_name']]);
        }

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check if the user is the last admin
        if ($user->hasRole('ADMIN') && User::whereHas('roles', function ($query) {
            $query->where('name', 'ADMIN');
        })->count() <= 1) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete the last admin user.']);
        }
        // Check if the user is the currently authenticated user
        elseif ($user->id === Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete the currently authenticated user.']);
        }
        // Check if the user has an instructor instance
        elseif ($user->hasRole('INSTRUCTOR') && $user->instructor) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete a user with an instructor instance.']);
        }
        // Check if the user has any associated courses
        elseif ($user->courses()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete a user with associated courses.']);
        }
        // Check if the user has any associated students
        elseif ($user->student) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete a user with associated students.']);
        }
        // Check if the user has any associated instructors
        elseif ($user->instructor) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete a user with associated instructors.']);
        }
        // Check if the user has any associated roles
        elseif ($user->roles()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete a user with associated roles.']);
        }
        // Check if the user has any associated permissions
        elseif ($user->permissions()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete a user with associated permissions.']);
        }
        elseif ($user->is_deleted) {
            return redirect()->back()->withErrors(['error' => 'User is already deleted.']);
        }
        else {
            // If no associated roles, permissions, courses, students, instructors, or is_deleted, proceed with deletion
            $user->delete();

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        }
    }

    public function updateProfilePicture(Request $request, User $user)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'profile_image' => 'required|image|max:10240|mimes:jpeg,png,jpg,JPG,gif,svg,webp',
        ]);

        $user = User::findOrFail($request->user_id);

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'User not found']);
        }
        $image = $request->file('profile_image');

        $img = Image::make($image)->fit(300, 300, function ($constraint) {
            $constraint->upsize();
        });

        $name = preg_replace('/\s+/', '', $user->name);

        $filename = 'profile_' . strtolower($name) . '.' . $image->getClientOriginalExtension();
        $path = 'profile_images/' . $filename;

        Storage::disk('public')->put($path, (string) $img->encode());

        if ($user->profile_img && Storage::disk('public')->exists($user->profile_img)) {
            Storage::disk('public')->delete($user->profile_img);
        }

        $user->profile_img = $path;
        $user->save();
    }
}
