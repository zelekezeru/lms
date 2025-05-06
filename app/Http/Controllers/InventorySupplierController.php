<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventorySupplierStoreRequest;
use App\Http\Requests\InventorySupplierUpdateRequest;
use App\Http\Resources\InventorySupplierResource;
use App\Models\InventorySupplier;

class InventorySupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventorySuppliers = InventorySupplierResource::collection(InventorySupplier::paginate(15));

        return inertia('InventorySuppliers/Index', [
            'inventorySuppliers' => $inventorySuppliers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return inertia('InventorySuppliers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventorySupplierStoreRequest $request)
    {
        $fields = $request->validated();

        $inventorySupplier = InventorySupplier::create($fields);

        return redirect()->route('inventorySuppliers.show', $inventorySupplier)->with('success', 'Inventory Supplier created successfully.');
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

        // Update the inventory supplier record
        $inventorySupplier->update($fields);

        return redirect()->route('inventorySuppliers.show', $inventorySupplier)->with('success', 'Inventory Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventorySupplier $inventorySupplier)
    {
        $inventorySupplier->delete();

        return redirect()->route('inventorySuppliers.index')->with('success', 'Inventory Supplier deleted successfully.');
    }
}
