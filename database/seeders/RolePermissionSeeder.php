<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear any cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define CRUD style permissions for each resource

        $permissions = [
            // Roles permissions (CRUD)
            'view-roles', 
            'create-roles', 
            'update-roles', 
            'delete-roles',  

            // Additional routes for roles
            'assign-permissions-roles', 
            'attach-permissions-roles', 
            'detach-permissions-roles', 

            // Permissions resource (CRUD)
            'view-permissions',
            'create-permissions',
            'update-permissions',
            'delete-permissions',

            // Students (CRUD)
            'view-students',
            'create-students',
            'update-students',
            'delete-students',

            // Departments (CRUD)
            'view-departments',
            'create-departments',
            'update-departments',
            'delete-departments',

            // Programs (CRUD)
            'view-programs',
            'create-programs',
            'update-programs',
            'delete-programs',

            // Employees (CRUD)
            'view-employees',
            'create-employees',
            'update-employees',
            'delete-employees',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles (formatted as requested)
        $roles = [
            'SUPER-ADMIN',
            'ADMIN',
            'PRESIDENT',
            'CAMPUS-DEAN',
            'SCHOOL-HEAD',
            'DEPARTMENT-HEAD',
            'QUALITY-ASSURANCE',
            'REGISTRAR',
            'HUMAN-RESOURCE',
            'INSTRUCTOR',
            'STUDENT',
        ];

        // Create roles and assign all permissions to SUPER-ADMIN and ADMIN
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if (in_array($roleName, ['SUPER-ADMIN', 'ADMIN'])) {
                $role->syncPermissions($permissions);
            }
        }
    }
}
