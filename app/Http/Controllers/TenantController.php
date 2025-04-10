<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\TenantStoreRequest;
use App\Http\Requests\TenantUpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\TenantResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Auth\RegisteredUserController;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tenant::query();
    
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
        }
        
        $allowedSorts = ['name', 'code', 'email', 'phone'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;
        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $tenants = TenantResource::collection($query->paginate(15)->withQueryString());
    
        return inertia('Tenants/Index', [
            'tenants' => $tenants,
            'search' => $request->search, // Keep the search term in the frontend
            'sortInfo' => [
                "currentSortColumn" => $sortColumn,
                "currentSortDirection" => $sortDirection,
            ]
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {        
        // Check if the user with id 1 already exists
        if(count(User::get()) == 1){
            // Get the user with id 1
            // This is the first user, so we can proceed with the tenant creation
            
            $user = UserResource::collection(User::all());
        
            return inertia('Tenants/Create', [
                'user' => $user,
            ]);

        }
        else
        {
            $user = UserResource::collection(User::all());
        
            return inertia('Tenants/Create', [
                'user' => $user,
            ]);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantStoreRequest $request)
    {
        $fields = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('tenants-logo', 'public');
            $fields['logo'] = '/storage/' . $logoPath;
        } else {
            unset($fields['logo']); 
        }
        
        // Creating Tenant Code
        $year = substr(Carbon::now()->year, -2);

        $fields['code'] = 'Tenant/'. str_pad(Tenant::count() + 1, 4, '0', STR_PAD_LEFT) . '/' . $year;
        
        // Creating Tenant Password
        $phone_end = substr($fields['phone'], -4);

        $tenant_password = $phone_end . '@lms';

        $fields['password'] = Hash::make($tenant_password);

        $fields['default_password'] = $tenant_password;
        
        // Create a new Tenant instance
        $tenant = Tenant::create($fields);

        // Create a new Tenant Admin in User table
        $user_phone = substr($fields['contact_phone'], -4);

        $user_password = $fields['name'] . '@' . $user_phone;

        // Merge the default password into the request
        $request->merge([
            'password' => $user_password,
            'password_confirmation' => $user_password, // needed for 'confirmed' rule
        ]);
        
        $registeredUserController = new RegisteredUserController();

        $Representative = $registeredUserController->store($request, 'TENANT-ADMIN', 'User');

        // Creating Representative as ADMIN user for the Tenant
        
        return redirect(route('tenants.show', $tenant));
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        return inertia('Tenants/Show', [
            'tenant' => new TenantResource($tenant),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {        
        return inertia('Tenants/Edit', [
            'tenant' => new TenantResource($tenant),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TenantUpdateRequest $request, Tenant $tenant)
    {
        $fields = $request->validated();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('tenants-logo', 'public');
            $fields['logo'] = '/storage/' . $logoPath;
        } else {
            unset($fields['logo']); 
        }
        
        $tenant->update($fields);
    
        return inertia('Tenants/Show', [
            'tenant' => new TenantResource($tenant),
        ]);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->update([
            'status' => 'inactive',
            'deleted_at' => now(),
            'deleted_by' => Auth::user()->id,
        ]);

        dd($tenant);
        // Later to be modified
        $tenant->delete();

        return redirect(route('tenants.index'));
    }

    public function tenantRepresentative($fields, $tenant_id)
    {
        
        // Set the default password
        
        $phone_end = substr($fields['contact_phone'], -4);
        $user_password = $fields['name'] . '@' . $phone_end;
        
        $registeredUserController = new RegisteredUserController();

        $userUuid = $registeredUserController->userUuid('ADMIN', 'User', $tenant_id);
        // Creating Representative as ADMIN user for the Tenant
        
        $user = User::create([
            'name' => $fields['contact_person'],
            'email' => $fields['contact_email'],
            'password' => Hash::make($user_password),
            'user_uuid' => $userUuid,
            'tenant_id' => $tenant_id,
        ]);
        
        $user->assignRole('ADMIN');

        return $user;
    }
}
