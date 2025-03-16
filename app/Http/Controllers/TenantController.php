<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\TenantStoreRequest;
use App\Http\Requests\TenantUpdateRequest;
use App\Http\Resources\TenantResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = TenantResource::collection(Tenant::paginate(10));

        return inertia('Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return  inertia('Tenants/Create');
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantStoreRequest $request)
    {
        $fields = $request->validated();

        $fields['password'] = 'tenant@' . $request->name;

        $year = substr(Carbon::now()->year, -2);

        $fields['code'] = 'Tenant/'. str_pad(Tenant::count() + 1, 4, '0', STR_PAD_LEFT) . '/' . $year;
        
        $tenant = Tenant::create($fields);
        
        return redirect(route('tenants.index'));
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

        $tenant->update($fields);

        return redirect(route('tenants.index'));
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
}
