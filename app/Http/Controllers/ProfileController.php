<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Student;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;

class ProfileController extends Controller
{
    /**
     * Display the user's profile view page.
     */
    public function show(Request $request): Response
    {
        if (!$request->user()) {
            return Inertia::render('Auth/Redirect', [
                'redirectTo' => route('login'),
            ]);
        }

        $role = $request->user()->getRoleNames()->first();

        if (!$role) {
            return Inertia::render('Auth/Redirect', [
                'redirectTo' => route('login'),
            ]);
        }

        $user = new UserResource($request->user());

        switch ($role) {
            case 'ADMIN':
                return Inertia::render('Profile/ProfileShow/AdminView', [
                    'user' => $user,
                ]);
            case 'INSTRUCTOR':
                return Inertia::render('Profile/ProfileShow/InstructorView', [
                    'user' => $user,
                ]);
            case 'REGISTRAR':
                return Inertia::render('Profile/ProfileShow/RegistrarView', [
                    'user' => $user,
                ]);
            case 'STUDENT':
                return Inertia::render('Profile/ProfileShow/StudentView', [
                    'user' => $user,
                ]);
            case 'Employee':
                return Inertia::render('Profile/ProfileShow/EmployeeView', [
                    'user' => $user,
                ]);
            case 'USER':
                return Inertia::render('Profile/ProfileShow/UserView', [
                    'user' => $user,
                ]);
            default:
                return Inertia::render('Auth/Redirect', [
                    'redirectTo' => route('login'),
                ]);
        }
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        // Check if the user has the 'student' role
        if (!$request->user()) {
            return back();
        }

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Ensure $image and $user are defined before calling saveProfileImage
        if ($request->hasFile('profile_img')) {
            $image = $request->file('profile_img');
            $user = $request->user();
            $request['profile_img'] = $this->saveProfileImage($image, $user);
        }
        // Check if the user has the 'student' role
        if (!$request->user()) {
            return Redirect::route('login');
        }

        if ($request->user()->isDirty('password')) {

            $request->user()->password = bcrypt($request->input('password'));
        }

        $data = [
            'name' => $request->input('name'),
            'profile_img' => $request->input('profile_img'),
        ];

        $request->user()->update($data);

        $role = $request->user()->getRoleNames()->first();

        if (!$role) {
            return Redirect::route('login');
        } elseif ($role === 'ADMIN') {

            return Redirect::back()->with('success', 'Profile updated successfully.');
        } else {
            return Redirect::route('login');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $role = $request->user()->getRoleNames()->first();

        if ($role === 'ADMIN') {

            $request->validate([
                'password' => ['required', 'current_password'],
            ]);

            $user = $request->user();

            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/');
        } else {
            return Redirect::route('login')->with('error', 'You are not authorized to perform this action.');
        }
    }
    /**
     * Update the user's profile image.
     */
    function saveProfileImage($image, $user)
    {
        $img = Image::make($image)->fit(300, 300, function ($constraint) {
            $constraint->upsize();
        });

        $name = preg_replace('/\s+/', '', $user->name);

        $filename = 'profile_' . strToLower($name) . '.' . $image->getClientOriginalExtension();
        $path = 'profile_images/' . $filename;

        Storage::disk('public')->put($path, (string) $img->encode());

        if ($user->profile_img && Storage::disk('public')->exists($user->profile_img)) {
            Storage::disk('public')->delete($user->profile_img);
        }

        $user->profile_img = $path;
        $user->save();

        return $path;
    }
}
