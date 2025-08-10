<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if the user has a default password set
        if ($request->user()->default_password) {
            // Redirect to change password page
            return redirect()->route('password.change')->with([
                'user' => $request->user(),
            ]);
        }

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function switchRole(Request $request)
    {
        if ($request->has('role')) {
            $role = Role::where('name', $request->role)->first();
            if (!$role) {
                return back()->withErrors(['role' => 'Role not found.']);
            }

            $request->user()->active_role_id = $role->id;
            $request->user()->save();

            $roleName = strtoupper($request->role);

            return redirect('/');
        }
    }

    // Route::get('change-password/{user}', [AuthenticatedSessionController::class, 'change'])->name('password.change');
    // build the method to handle password change
    public function change()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to change your password.');
        }
        return Inertia::render('Auth/ChangePassword', [
            'user' => $user,
            'status' => session('status'),
        ]);
    }
}
