<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
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
        $users = UserResource::collection(User::paginate(15));

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validated();

        $registeredUserController = new RegisteredUserController;

        $user = $registeredUserController->store($request, 'USER', 'User');

        $image = $request->file('profile_img');

        if ($image) {
            $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
            $path = storage_path('app/public/profile-images/'.$imageName);

            Image::make($image)->resize(300, 300)->save($path);

            $user->update([
                'profile_img' => 'profile-images/'.$imageName,
            ]);
        }

        $user->assignRole($fields['role_name']);

        return redirect()->route('users.show', $user)->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return inertia('Users/Show', [
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return inertia('Users/Edit', [
            'user' => new UserResource($user->load('tenant')),
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $fields = $request->validated();

        // Handle profile image upload
        $profileImg = $request->file('profile_img');

        if ($profileImg) {
            // Delete old image if exists
            if ($user->profile_img) {
                Storage::disk('public')->delete($user->profile_img);
            }

            $imageName = Str::uuid().'.'.$profileImg->getClientOriginalExtension();
            $path = storage_path('app/public/profile-images/'.$imageName);

            Image::make($profileImg)->resize(300, 300)->save($path);

            $user->profile_img = 'profile-images/'.$imageName;
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
        // cHANGE THE DELETED AT TO THE CURRENT DATE
        $user->deleted_at = Carbon::now();
        $user->deleted_by_name = Auth::user()->id;
        $user->is_deleted = true;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
