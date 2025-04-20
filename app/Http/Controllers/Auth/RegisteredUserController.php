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
    public function store(Request $request, $role = null, $model= null): RedirectResponse
    {
        $fields = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:15'],
            'default_password' => ['nullable', 'string', 'max:15'],
        ]);

        // SUPER-ADMIN User

        if(User::where('id', 1)->first() == null){
            // Get the user with id 1
            $year = substr(Carbon::now()->year, -2); // get current year's last two digits

            $user = User::create([
                'id' => 1,
                'name' => $request->name,
                'tenant_id' => 1,
                'user_uuid' => 'SA' .  '/' . $year,
                'email' => $request->email,
                'phone' => $request->contact,
                'profile_img' => $request->profile_img,
                'default_password' => $request->password,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('SUPER-ADMIN');

            event(new Registered($user));
    
            Auth::login($user);
    
            return redirect(route('dashboard'));
        }

        // TENANT-ADMIN User
        elseif($request->contact_phone != null){

            $userUuid = $this->userUuid('TENANT-ADMIN', 'Tenant');

            $tenant = Tenant::first();

            $fields['tenant_id'] = $tenant->id;

            $fullName = trim($request->name); // Remove extra spaces
            $nameParts = explode(' ', $fullName);

            // Fallback values in case there’s only one name part
            $firstName = strtolower($nameParts[0]);
            $secondName = isset($nameParts[1]) ? strtolower($nameParts[1]) : 'user';

            // Create the email
            $email = $firstName . '.' . $secondName . '@sits.edu.et';

        // Assign to your fields array
        $fields['email'] = $email;
            
            $user = User::create([
                'tenant_id' => $fields['tenant_id'],
                'name' => $request->name,
                'user_uuid' => $userUuid,
                'email' => $request->email,
                'phone'=> $request->contact_phone,
                'default_password' => $request->password,
                'profile_img' => $request->profile_img,
                'password' => Hash::make($request->password),
                'password_changed' => $request->password_changed

            ]);
                $user->assignRole('TENANT-ADMIN');

            event(new Registered($user));

            return $user;

        }
        //Instructor User
        elseif($request->contact_phone != null && $request->contact_email == null){
            $userUuid = $this->userUuid('INSTRUCTOR', 'Instructor');
            $fields['name'] = $request->contact_person;
            $fields['phone'] = $request->contact_phone;
            $fields['default_password'] = $request->password;
            $fields['profile_img'] = $request->profile_img;
            
            $user = User::create([
                'tenant_id' => 1,
                'name' => $fields['name'],
                'user_uuid' => $userUuid,
                'email' => $request->contact_email,
                'phone'=> $fields['phone'],
                'profile_img' => $fields['profile_img'],
                'default_password' => $fields['default_password'],
                'password' => Hash::make($fields['default_password']),
            ]);
            $user->assignRole('INSTRUCTOR');

            return redirect(route('instructors.show', $user->id))->with('success', 'Instructor created successfully.');
        }

        // Student User
        elseif($request->contact_email != null){
            $userUuid = $this->userUuid('STUDENT', 'Student');
            $fields['name'] = $request->contact_email;
            $fields['phone'] = $request->contact_phone;
            $fields['default_password'] = $request->password;
            $fields['profile_img'] = $request->profile_img;
            
        }
        // All other Users
        else{

            $userUuid = $this->userUuid('USER', 'User');

            $user = User::create([
                'tenant_id' => 1,
                'name' => $request->name,
                'user_uuid' => $userUuid,
                'email' => $request->email,
                'phone'=> $request->contact,
                'profile_img' => $request->profile_img,
                'default_password' => $request->password,
                'password' => Hash::make($request->password),
            ]);
                $user->assignRole('USER');
        }
    }

    public function userUuid($role, $type, $tenant_id = null)
    {
        $userUuid = null;
        
        if($tenant_id == null){
            $tenant = Tenant::first();
        }else{
            $tenant = Tenant::find($tenant_id);
        }
        if(!$tenant){

            return redirect(route('tenants.create'));

        }else{ 

            $year = substr(Carbon::now()->year, -2); // get current year's last two digits
            
            if($role == 'TENANT-ADMIN') {
                $userUuid = $tenant->name . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
            }elseif($role == 'ADMIN') {
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
