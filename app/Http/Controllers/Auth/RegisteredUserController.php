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
    public function store(Request $request, $role = null, $model= null, $parent = null): RedirectResponse
    {   
        // Check if the user with id 1 already exists
        if(User::where('id', 1)->exists()){
            return redirect(route('employees.create'));
        }

        $year = substr(Carbon::now()->year, -2); // get current year's last two digits

        // SUPER-ADMIN Registration

        // Check if the user with id 1 already exists
        if(User::where('id', 1)->first() == null){
            
            $fields = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'phone' => ['nullable', 'string', 'max:15'],
                'email'=> ['required', 'email', 'unique:users,email'],
                'profile_img' => ['nullable', 'image:jpg,jpeg,png,gif,svg,webp|max:5150'],
            ]);

            $user = User::create([
                'id' => 1,
                'name' => $request->name,
                'tenant_id' => 1,
                'user_uuid' => 'SA' .  '/' . $year,
                'email' => $request->email,
                'phone' => $request->contact_phone,
                'profile_img' => $request->hasFile('profile_img') 
                    ? $request->file('profile_img')->store('profile_images') 
                    : null,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('SUPER-ADMIN');

            event(new Registered($user));
    
            Auth::login($user);
    
            return redirect(route('dashboard'));
        }


        else{
                // Construct Form Elements

                $fields = $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'contact_phone' => ['nullable', 'string', 'max:15'],
                ]);

                // Professional Email
                $fullName = trim($request->name); // Remove extra spaces
                $nameParts = explode(' ', $fullName);

                // Fallback values in case there’s only one name part
                $firstName = strtolower($nameParts[0]);
                $secondName = isset($nameParts[1]) ? strtolower($nameParts[1]) : 'user';

                // Create the email
                $email = $firstName . '.' . $secondName . '@sits.edu.et';


                // ID Generation
                $userUuid = $this->userUuid($role, $model, $tenant_id = null);

                if ($userUuid) {
                    $fields['uuid'] = $userUuid;
                }else{
                    'Not Set';
                }

                if($tenant_id == null){
                    $tenant = Tenant::first();
                }else{
                    $tenant = Tenant::find($tenant_id);
                }
                if(!$tenant){

                    return redirect(route('tenants.create'));
                }
                // Fields Form fill

                $fields = [
                    'name'=> $request['name'],
                    'user_uuid' => $userUuid,
                    'phone'=> $request->contact_phone,
                    'email' => $email,
                    'tenant_id' => $tenant->id,
                    'password' => Hash::make($request['password']),
                    'default_password' => $request['default_password'],
                    'profile_img' => $request->hasFile('profile_img') 
                        ? $request->file('profile_img')->store('profile_images') 
                        : null, // or you can use a default image path here
                ];
            
            // TENANT-ADMIN User
            if($role == 'TENANT-ADMIN'){
                
                $user = User::create($fields);
                
                $parent->update([
                    'user_id' => $user->id,
                ]);

                $user->assignRole('TENANT-ADMIN');

                event(new Registered($user));

                return redirect(route('tenants.show', $user->tenant_id))->with('success', 'Tenant created successfully.');

            }

            //Instructor User
            elseif($role == 'INSTRUCTOR'){
                
                $user = User::create($fields);

                $parent->update([
                    'user_id' => $user->id,
                ]);

                $user->assignRole('INSTRUCTOR');
                
                return redirect(route('instructors.show', $parent->id))->with('success', 'Instructor created successfully.');
            }
            // Employee User
            elseif($role == 'EMPLOYEE')
            {
                $user = User::create($fields);

                $parent->update([
                    'user_id' => $user->id,
                ]);

                $user->assignRole();
                
                return redirect(route('employees.show', $parent->id))->with('success', 'Employee created successfully.');
            }
            // All other Users
            else{
                
                $user = User::create($fields);

                $user->assignRole('USER');

                return redirect(route('users.show', $user->id))->with('success','User created successfully.');
            }
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
                $userUuid = $tenant->name . '-'. substr($role, 0, 2) . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
            }elseif($role == 'ADMIN') {
                $userUuid = $tenant->name . '-'. substr($role, 0, 2) . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
            }elseif($role == 'USER') {
                $userUuid = $tenant->name . '-'. substr($role, 0, 2) . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
            }  elseif($role == 'EMPLOYEE') {
                $userUuid = $tenant->name . '-'. substr($role, 0, 2) . '-' . str_pad(Employee::count() + 1, 3, '0', STR_PAD_LEFT);
            } elseif($role == 'INSTRUCTOR') {
                $userUuid = $tenant->name . '-'. substr($role, 0, 2) . '-' . str_pad(Instructor::count() + 1, 3, '0', STR_PAD_LEFT);
            }elseif($role == 'STUDENT') {
                $userUuid = $tenant->name . '-' . $year . '-'. substr($role, 0, 2) . '-' . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        }
        return $userUuid;
    }
}
