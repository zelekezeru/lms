<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Tenant;
use App\Models\Student;
use App\Models\Instructor;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        if(User::where('id', 1)->exists()){
            return redirect(route('employees.create'));
        }
        else
        {
            return Inertia::render('Auth/Register');
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $userUuid = $this->userUuid('SUPER-ADMIN');

        $user = User::create([
            'id' => 1,
            'tenant_id' => 1,
            'name' => $request->name,
            'user_uuid' => $userUuid,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($user->id == 1){
            $user->assignRole('SUPER-ADMIN');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }

    public function userUuid($role, $type)
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two digits
        
        $tenant = Tenant::first()->name; // get the first tenant name
        
        if($type == 'STUDENT') {
            $userUuid = $tenant . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
        } elseif($type == 'INSTRUCTOR') {
            $userUuid = $tenant . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(Instructor::count() + 1, 3, '0', STR_PAD_LEFT);
        } elseif($type == 'EMPLOYEE') {
            $userUuid = $tenant . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(Employee::count() + 1, 3, '0', STR_PAD_LEFT);
        }
        else {
            $userUuid = $tenant . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
        }      
        
        // Example: 'TENANT-23-EM-0001'
        return $userUuid;
    }
}
