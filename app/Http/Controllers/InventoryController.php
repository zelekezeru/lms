<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Http\Requests\InventoryStoreRequest;
use App\Http\Requests\InventoryUpdateRequest;
use App\Http\Resources\InventoryCategoryResource;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\InventorySupplierResource;
use App\Models\InventoryCategory;
use App\Models\InventorySupplier;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = InventoryResource::collection(Inventory::paginate(15));
        
        return inertia('Inventories/Index', [
            'inventories' => $inventories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $inventoryCategories = InventoryCategoryResource::collection(InventoryCategory::all());
        $inventorySuppliers = InventorySupplierResource::collection(InventorySupplier::all());
        return  inertia('Inventories/Create', compact('inventoryCategories', 'inventorySuppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventoryStoreRequest $request)
    {
        $fields = $request->validated();
        
        $inventory = Inventory::create($fields);
        
        return redirect()->route('inventories.show', $inventory)->with('success', 'Inventory created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return inertia('Inventories/Show', [
            'inventory' => new InventoryResource($inventory),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $inventoryCategories = InventoryCategoryResource::collection(InventoryCategory::all());
        $inventorySuppliers = InventorySupplierResource::collection(InventorySupplier::all());
        $inventory = new InventoryResource($inventory);

        return inertia('Inventories/Edit', [
            'inventory' => $inventory,
            'inventoryCategories' => $inventoryCategories,
            'inventorySuppliers' => $inventorySuppliers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventoryUpdateRequest $request, Inventory $inventory)
    {
        $fields = $request->validated();

        // Update the inventory record
        $inventory->update($fields);

        return redirect()->route('inventories.show', $inventory)->with('success', 'Inventory updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventories.index')->with('success', 'Inventory deleted successfully.');
    }
}
