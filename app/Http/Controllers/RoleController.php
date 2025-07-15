<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $roles = Role::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%$search%");
        })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('Roles/Index', compact('roles', 'search'));
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create');
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create($request->validated());

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.show', $role)->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $rolePermissions = $role->permissions;

        return Inertia::render('Roles/Show', [
            'role' => $role,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function edit(Role $role): Response
    {
        $permissions = Permission::all(); // Fetch all permissions to allow assignment during editing
        $attachedPermissions = $role->permissions()->pluck('id'); // Fetch currently attached permissions

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'attachedPermissions' => $attachedPermissions,
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $fields = $request->validated();

        // Update the role details
        $role->update($fields);

        // Sync permissions if provided
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));

            $users = $role->users;

            foreach ($users as $user) {
                cache()->forget("user_roles_" . $user->id);

                cache()->forget("user_permissions_" . $user->id);

                cache()->remember("user_roles_" . $user->id, now()->addMinutes(10), function () use ($user) {
                    return $user->getRoleNames();
                });

                cache()->remember("user_permissions_" . $user->id, now()->addMinutes(10), function () use ($user) {
                    return $user->getAllPermissions()->pluck('name');
                });
            }
        }


        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.show', $role)->with('success', 'Role updated successfully.');
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
            ->paginate(50);

        return Inertia::render('Roles/Index', compact('roles'));
    }

    // Assign permissions to a role

    public function assign(Request $request, Role $role)
    {
        $permissions = Permission::get();

        // Fetch existing attached permissions
        $attachedPermissions = $role->permissions()->get()->pluck('id');

        return Inertia::render('Roles/Assign', compact('role', 'permissions', 'attachedPermissions'));
    }

    public function attach(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        // dump($request['perm']);
        // dd($role->guard_name);

        $role->syncPermissions($request['permissions']);

        $users = $role->users;

        foreach ($users as $user) {
            cache()->forget("user_roles_" . $user->id);

            cache()->forget("user_permissions_" . $user->id);

            cache()->remember("user_roles_" . $user->id, now()->addMinutes(10), function () use ($user) {
                return $user->getRoleNames();
            });

            cache()->remember("user_permissions_" . $user->id, now()->addMinutes(10), function () use ($user) {
                return $user->getAllPermissions()->pluck('name');
            });
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Permissions assigned successfully.');
    }

    public function detach(Request $request, $roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);

        $role->permissions()->detach($permissionId);

        return redirect()->route('roles.index')->with('success', 'Permission removed successfully.');
    }
}
