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
        dd('here');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'nullable',
            'type' => 'nullable',
        ]);

        // Check if the user with id 1 already exists
        if(!User::exists()){
            $year = substr(Carbon::now()->year, -2); // get current year's last two digits

            $user = User::create([
                'id' => 1,
                'name' => $request->name,
                'tenant_id' => 1,
                'user_uuid' => '000'. User::count() . '/' .$request->name .  '/' . $year,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('SUPER-ADMIN');

            event(new Registered($user));
    
            Auth::login($user);

            return redirect()->route('tenants.create');
        }
        else{

            $userUuid = $this->userUuid('USER', 'User');

            $user = User::create([
                'id' => 1,
                'tenant_id' => 1,
                'name' => $request->name,
                'user_uuid' => $userUuid,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
                $user->assignRole('SUPER-ADMIN');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }

    public function userUuid($role, $type, $tenant_id = 1)
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two digits
        
        $tenant = Tenant::find($tenant_id);

        if(!$tenant){
            return redirect(route('tenants.create'));
        }else{
            if($role == 'ADMIN') {
                $userUuid = $tenant->name . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
            }elseif($role == 'USER') {
                $userUuid = $tenant->name . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
            }  elseif($role == 'EMPLOYEE') {
                $userUuid = $tenant->name . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(Employee::count() + 1, 3, '0', STR_PAD_LEFT);
            }elseif($role == 'STUDENT') {
                $userUuid = $tenant->name . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
            } elseif($role == 'INSTRUCTOR') {
                $userUuid = $tenant->name . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(Instructor::count() + 1, 3, '0', STR_PAD_LEFT);
            }
        }

        return $userUuid;
    }
}
