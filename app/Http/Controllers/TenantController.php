<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\TenantStoreRequest;
use App\Http\Requests\TenantUpdateRequest;
use App\Http\Resources\TenantResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = TenantResource::collection(Tenant::paginate(15));

        return inertia('Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Tenants/Create');
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

        // Creating Tenant representative User
        $tenant = Tenant::create($fields);

        $this->tenantRepresentative($fields, $tenant->id);
        
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
        // Later to be modified
        $tenant->delete();

        return redirect(route('tenants.index'));
    }

    public function tenantRepresentative($fields, $tenant_id)
    {
        $phone_end = substr($fields['contact_phone'], -4);
        
        // Set the default password
        $user_password = $fields['name'] . '@' . $phone_end;

        // Representative ID 
        $year = substr(Carbon::now()->year, -2); // get current year's last two digits

        $representative_id = 'ADMIN/' . '000'. User::count() . '/' .$fields['name'] .  '/' . $year;

        // Creating Representative as ADMIN user for the Tenant

        $user = User::create([
            'name' => $fields['contact_person'],
            'email' => $fields['contact_email'],
            'password' => Hash::make($user_password),
            'user_uuid' => $representative_id,
            'tenant_id' => $tenant_id,
        ]);
        
        $user->assignRole('ADMIN');

        return $user;
    }

    public function representative_id($tenant)
    {
        $year = substr(Carbon::now()->year, -2); // get current year's last two digits

        $representative_id = $tenant . '/' . '0001'  . '/' . $year;

        return $representative_id;
    }
}
