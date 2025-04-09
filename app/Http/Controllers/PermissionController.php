<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\PermissionRequest;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $permissions = Permission::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%$search%");
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Permissions/Index', compact('permissions', 'search'));
    }

    public function create(): Response
    {
        return Inertia::render('Permissions/Create');
    }
    
    public function store(PermissionRequest $request)
    {
        
        $permission = Permission::create($request->validated());

        return redirect()->route('permissions.index')->with('success', 'Permission added successfully.');
    }
    
    public function show(Permission $permission)
    {
        return Inertia::render('Permissions/Show', [
            'permission' => $permission
        ]);
    }

    public function edit(Permission $permission): Response
    {
        return Inertia::render('Permissions/Edit', compact('permission'));
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());
        
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $permissions = Permission::where('permission_name', 'like', "%$search%")
            ->latest()
            ->paginate(15);
        return Inertia::render('Permissions/Index', compact('permissions'));
    }
}
