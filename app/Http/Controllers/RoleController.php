<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\RoleRequest;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::latest()->paginate(10);

        return Inertia::render('Roles/Index', compact('roles'));
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create');
    }
    
    public function store(RoleRequest $request)
    {
        
        $role = Role::create($request->validated());

        return redirect()->route('roles.index')->with('success', 'Role added successfully.');
    }
    
    public function show(Role $role)
    {
        // Fetch entries in 'role_has_permissions' table having the ID of this role
        $rolePermissions = DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $role->id)
            ->select('permissions.name')
            ->get();

        return Inertia::render('Roles/Show', [
            'role' => $role,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Roles/Edit', compact('role'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->update($request->validated());
        
        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $roles = Role::where('role_name', 'like', "%$search%")
            ->latest()
            ->paginate(10);
        return Inertia::render('Roles/Index', compact('roles'));
    }

    // Assign permissions to a role

    public function assign(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        $permissions = Permission::latest()->get();

        // Fetch existing attached permissions
        $attachedPermissions = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->pluck('permission_id')
            ->toArray();

        return Inertia::render('Roles/Assign', compact('role', 'permissions', 'attachedPermissions'));
    }

    public function attach(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        foreach ($request->permissions as $permissionId) {
            // Check if the permission is already attached to the role
            $exists = DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$exists) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        return redirect()->route('roles.index')->with('success', 'Permissions assigned successfully.');
    }

    public function detach(Request $request, $roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);

        $role->permissions()->detach($permissionId);

        return redirect()->route('roles.index')->with('success', 'Permission removed successfully.');
    }
}
