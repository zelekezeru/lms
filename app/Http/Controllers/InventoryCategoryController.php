<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryCategoryStoreRequest;
use App\Http\Requests\InventoryCategoryUpdateRequest;
use App\Http\Resources\InventoryCategoryResource;
use App\Models\InventoryCategory;

class InventoryCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventoryCategories = InventoryCategoryResource::collection(InventoryCategory::paginate(15));

        return inertia('InventoryCategories/Index', [
            'inventoryCategories' => $inventoryCategories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return inertia('InventoryCategories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventoryCategoryStoreRequest $request)
    {
        $fields = $request->validated();

        $inventoryCategory = InventoryCategory::create($fields);

        return redirect(route('inventoryCategories.show', $inventoryCategory))->with('success', 'InventoryCategory created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryCategory $inventoryCategory)
    {
        return inertia('InventoryCategories/Show', [
            'inventoryCategory' => new InventoryCategoryResource($inventoryCategory),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryCategory $inventoryCategory)
    {
        return inertia('InventoryCategories/Edit', [
            'inventoryCategory' => new InventoryCategoryResource($inventoryCategory),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventoryCategoryUpdateRequest $request, InventoryCategory $inventoryCategory)
    {
        $fields = $request->validated();

        // Update the inventory category
        $inventoryCategory->update($fields);

        return redirect(route('inventoryCategories.show', $inventoryCategory))->with('success', 'Inventory Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryCategory $inventoryCategory)
    {
        $inventoryCategory->delete();

        return redirect()->route('inventoryCategories.index')->with('success', 'Inventory Category deleted successfully.');
    }
}
