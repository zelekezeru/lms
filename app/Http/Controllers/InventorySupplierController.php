<?php

namespace App\Http\Controllers;

use App\Models\InventorySupplier;
use App\Http\Requests\InventorySupplierStoreRequest;
use App\Http\Requests\InventorySupplierUpdateRequest;
use App\Http\Resources\InventorySupplierResource;
use Illuminate\Http\Request;

class InventorySupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventorySuppliers = InventorySupplierResource::collection(InventorySupplier::paginate(10));

        return inertia('InventorySuppliers/Index', [
            'inventorySuppliers' => $inventorySuppliers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return  inertia('InventorySuppliers/Create');
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(InventorySupplierStoreRequest $request)
    {
        $fields = $request->validated();
        
        $inventorySupplier = InventorySupplier::create($fields);
        
        return redirect(route('inventorySuppliers.index'));
    }
    
    /**
     * Display the specified resource.
     */
    public function show(InventorySupplier $inventorySupplier)
    {
        return inertia('InventorySuppliers/Show', [
            'inventorySupplier' => new InventorySupplierResource($inventorySupplier),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventorySupplier $inventorySupplier)
    {
        return inertia('InventorySuppliers/Edit', [
            'inventorySupplier' => new InventorySupplierResource($inventorySupplier),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventorySupplierUpdateRequest $request, InventorySupplier $inventorySupplier)
    {
        $fields = $request->validated();

        $inventorySupplier->update($fields);

        return redirect(route('inventorySuppliers.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventorySupplier $inventorySupplier)
    {
        // Later to be modified
        $inventorySupplier->delete();

        return redirect(route('inventorySuppliers.index'));
    }
}
