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

            // Tracks (CRUD)
            'view-tracks',
            'create-tracks',
            'update-tracks',
            'delete-tracks',

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

            // Year (CRUD)
            'view-years',
            'create-years',
            'update-years',
            'delete-years',
            // Semester (CRUD)
            'view-semesters',
            'create-semesters',
            'update-semesters',
            'delete-semesters',
            // Section (CRUD)
            'view-sections',
            'create-sections',
            'update-sections',
            'delete-sections',

            // Results (CRUD)
            'view-results',
            'create-results',
            'update-results',
            'delete-results',

            // Weights (CRUD)
            'view-weights',
            'create-weights',
            'update-weights',
            'delete-weights',

            // Grades (CRUD)
            'view-grades',
            'create-grades',
            'update-grades',
            'delete-grades',

            // Curriculum (CRUD)
            'view-curriculums',
            'create-curriculums',
            'update-curriculums',
            'delete-curriculums',

            // Payment (CRUD)
            'view-payments',
            'create-payments',
            'update-payments',
            'delete-payments',

            // Payment Categories (CRUD)
            'view-paymentCategories',
            'create-paymentCategories',
            'update-paymentCategories',
            'delete-paymentCategories',

            // Payment Items (CRUD)
            'view-paymentItems',
            'create-paymentItems',
            'update-paymentItems',
            'delete-paymentItems',            

            // Payment Schedule (CRUD)
            'view-paymentSchedules',
            'create-paymentSchedules',
            'update-paymentSchedules',
            'delete-paymentSchedules',

            // Assigning Relationships
            'section-courses',
            'attach-section-courses',
            'detach-section-courses',

            'section-instructors',
            'attach-section-instructors',
            'detach-section-instructors',

            'section-students',
            'attach-section-students',
            'detach-section-students',

            'course-instructors',
            'attach-course-instructors',
            'detach-course-instructors',

            'course-students',
            'attach-course-students',
            'detach-course-students',
            
            'default-password',
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
            'PROGRAM-DIRECTOR',
            'REGISTRAR',
            'INSTRUCTOR',
            'STUDENT',
        ];

        // Create roles and assign all permissions to SUPER-ADMIN and ADMIN
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if (in_array($roleName, ['SUPER-ADMIN'])) {
                $role->syncPermissions($permissions);
            } elseif (in_array($roleName, ['TENANT-ADMIN', 'ADMIN'])) {
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

                    'view-tracks',
                    'create-tracks',
                    'update-tracks',
                    'delete-tracks',

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

                    'view-years',
                    'create-years',
                    'update-years',
                    'delete-years',

                    'view-semesters',
                    'create-semesters',
                    'update-semesters',
                    'delete-semesters',

                    'view-sections',
                    'create-sections',
                    'update-sections',
                    'delete-sections',

                    'view-results',
                    'create-results',
                    'update-results',
                    'delete-results',

                    'view-weights',
                    'create-weights',
                    'update-weights',
                    'delete-weights',

                    'view-grades',
                    'create-grades',
                    'update-grades',
                    'delete-grades',

                    'view-curriculums',
                    'create-curriculums',
                    'update-curriculums',
                    'delete-curriculums',

                    'section-courses',
                    'attach-section-courses',
                    'detach-section-courses',

                    'section-instructors',
                    'attach-section-instructors',
                    'detach-section-instructors',

                    'section-students',
                    'attach-section-students',
                    'detach-section-students',

                    'course-instructors',
                    'attach-course-instructors',
                    'detach-course-instructors',

                    'course-students',
                    'attach-course-students',
                    'detach-course-students',
                    'view-payments',
                    'create-payments',
                    'update-payments',
                    'delete-payments',

                    'view-paymentCategories',
                    'create-paymentCategories',
                    'update-paymentCategories',
                    'delete-paymentCategories',
                    
                    'view-paymentItems',
                    'create-paymentItems',
                    'update-paymentItems',
                    'delete-paymentItems',
                    
                    'view-paymentSchedules',
                    'create-paymentSchedules',
                    'update-paymentSchedules',
                    'delete-paymentSchedules',

                ]);
                $role->syncPermissions($tenantAdminPermissions);
            }
        }
    }
}
