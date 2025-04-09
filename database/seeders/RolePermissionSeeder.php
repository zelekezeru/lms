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

            // Permissions resource (CRUD)
            'view-tenants',
            'create-tenants',
            'update-tenants',
            'delete-tenants',

            // Programs (CRUD)
            'view-programs',
            'create-programs',
            'update-programs',
            'delete-programs',

            // StudyMode (CRUD)
            'view-studyModes',
            'create-studyModes',
            'update-studyModes',
            'delete-studyModes',

            // Departments (CRUD)
            'view-departments',
            'create-departments',
            'update-departments',
            'delete-departments',

            // Courses (CRUD)
            'view-courses',
            'create-courses',
            'update-courses',
            'delete-courses',

            // User (CRUD)
            'view-users',
            'create-users',
            'update-users',
            'delete-users',

            // Students (CRUD)
            'view-students',
            'create-students',
            'update-students',
            'delete-students',

            // Instructors (CRUD)
            'view-instructors',
            'create-instructors',
            'update-instructors',
            'delete-instructors',

            // Employees (CRUD)
            'view-employees',
            'create-employees',
            'update-employees',
            'delete-employees',

            // User Document (CRUD)
            'view-userDocuments',
            'create-userDocuments',
            'update-userDocuments',
            'delete-userDocuments',

            // Inventory (CRUD)
            'view-inventories',
            'create-inventories',
            'update-inventories',
            'delete-inventories',

            // Inventory Category (CRUD)
            'view-inventory-categories',
            'create-inventory-categories',
            'update-inventory-categories',
            'delete-inventory-categories',

            // Inventory Supplier (CRUD)
            'view-inventory-suppliers',
            'create-inventory-suppliers',
            'update-inventory-suppliers',
            'delete-inventory-suppliers',

            // Assigning Relationships
            'assign-courses-sections',
            'remove-courses-sections',
            'assign-instructors-sections',
            'remove-instructors-sections',
            'assign-instructors-courses',
            'remove-instructors-courses',
            'assign-students-sections',
            'remove-students-sections',
            'assign-students-courses',
            'remove-students-courses',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles (formatted as requested)
        $roles = [
            'SUPER-ADMIN',
            'TENANT-ADMIN',
            'ADMIN',
            'PRESIDENT',
            'DEPARTMENT-HEAD',
            'PROGRAM-DIRECTOR',
            'CAMPUS-DEAN',
            'SCHOOL-HEAD',
            'QUALITY-ASSURANCE',
            'REGISTRAR',
            'HUMAN-RESOURCE',
            'INSTRUCTOR',
            'STUDENT',
        ];

        // Create roles and assign all permissions to SUPER-ADMIN and ADMIN
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if (in_array($roleName, ['SUPER-ADMIN'])) {
                $role->syncPermissions($permissions);
            } 
            elseif (in_array($roleName, ['TENANT-ADMIN', 'ADMIN'])) {
                $tenantAdminPermissions = array_diff($permissions, [
                    'view-roles', 
                    'create-roles', 
                    'update-roles', 
                    'delete-roles',  

                    'assign-permissions-roles', 
                    'attach-permissions-roles', 
                    'detach-permissions-roles', 

                    'view-permissions',
                    'create-permissions',
                    'update-permissions',
                    'delete-permissions',

                    'view-programs',
                    'create-programs',
                    'update-programs',
                    'delete-programs',

                    'view-studyModes',
                    'create-studyModes',
                    'update-studyModes',
                    'delete-studyModes',

                    'view-departments',
                    'create-departments',
                    'update-departments',
                    'delete-departments',
                    
                    'view-courses',
                    'create-courses',
                    'update-courses',
                    'delete-courses',

                    'view-users',
                    'create-users',
                    'update-users',
                    'delete-users',

                    'view-students',
                    'create-students',
                    'update-students',
                    'delete-students',

                    'view-instructors',
                    'create-instructors',
                    'update-instructors',
                    'delete-instructors',

                    'view-employees',
                    'create-employees',
                    'update-employees',
                    'delete-employees',

                    'view-userDocuments',
                    'create-userDocuments',
                    'update-userDocuments',
                    'delete-userDocuments',

                    'view-inventories',
                    'create-inventories',
                    'update-inventories',
                    'delete-inventories',

                    'view-inventory-suppliers',
                    'create-inventory-suppliers',
                    'update-inventory-suppliers',
                    'delete-inventory-suppliers',

                    'view-inventory-categories',
                    'create-inventory-categories',
                    'update-inventory-categories',
                    'delete-inventory-categories',

                    'assign-courses-sections',
                    'remove-courses-sections',
                    'assign-instructors-sections',
                    'remove-instructors-sections',
                    'assign-instructors-courses',
                    'remove-instructors-courses',
                    'assign-students-sections',
                    'remove-students-sections',
                    'assign-students-courses',
                    'remove-students-courses',

                ]);
                $role->syncPermissions($tenantAdminPermissions);
            }
        }
    }
}
