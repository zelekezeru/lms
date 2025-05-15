<?php

namespace Database\Seeders;

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

            // Permissions resource (CRUD)
            'view-tenants',
            'create-tenants',
            'update-tenants',
            'delete-tenants',

            // Roles permissions (CRUD)
            'view-roles',
            'create-roles',
            'update-roles',
            'delete-roles',

            // Permissions resource (CRUD)
            'view-permissions',
            'create-permissions',
            'update-permissions',
            'delete-permissions',

            // Additional routes for roles
            'assign-permissions-roles',
            'attach-permissions-roles',
            'detach-permissions-roles',

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

            // Payment Method (CRUD)
            'view-paymentMethods',
            'create-paymentMethods',
            'update-paymentMethods',
            'delete-paymentMethods',

            // Payment Type (CRUD)
            'view-paymentTypes',
            'create-paymentTypes',
            'update-paymentTypes',
            'delete-paymentTypes',

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

            // User Documents
            'update-user-document',
            'update-user-document-image',

            // Password Related
            'default-password',
            'change-password',
            'reset-password',
            'forgot-password',
            'update-password',
            'update-profile',
            'update-profile-image',
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
            'EMPLOYEE',
            'INVENTORY-ADMIN',
            'INVENTORY-USER',
            'COORDINATOR',
            'FINANCE-ADMIN',
            'FINANCE-USER',
            'LIBRARY-ADMIN',
            'LIBRARY-USER',
            'IT-ADMIN',
            'IT-USER',
            'HR-ADMIN',
            'HR-USER',
        ];

        // Create roles and assign all permissions to SUPER-ADMIN and ADMIN
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if (in_array($roleName, ['SUPER-ADMIN'])) {
                $role->syncPermissions($permissions);
            } elseif (in_array($roleName, ['TENANT-ADMIN', 'ADMIN'])) {
                $tenantAdminPermissions = array_diff($permissions, [

                    // Except these permissions
                    'view-tenants',
                    'create-tenants',
                    'update-tenants',
                    'delete-tenants',

                ]);
                $role->syncPermissions($tenantAdminPermissions);
            }
            elseif (in_array($roleName, ['STUDENT', 'STUDENT'])) {
                $studentPermissions = array_diff($permissions, [

                    // Except these permissions

                    // Permissions resource (CRUD)
                    'view-tenants',
                    'create-tenants',
                    'update-tenants',
                    'delete-tenants',

                    // Roles permissions (CRUD)
                    'view-roles',
                    'create-roles',
                    'update-roles',
                    'delete-roles',

                    // Permissions resource (CRUD)
                    'view-permissions',
                    'create-permissions',
                    'update-permissions',
                    'delete-permissions',

                    // Additional routes for roles
                    'assign-permissions-roles',
                    'attach-permissions-roles',
                    'detach-permissions-roles',

                    // Programs (CRUD)
                    'create-programs',
                    'update-programs',
                    'delete-programs',

                    // StudyMode (CRUD)
                    'create-studyModes',
                    'update-studyModes',
                    'delete-studyModes',

                    // Tracks (CRUD)
                    'create-tracks',
                    'update-tracks',
                    'delete-tracks',

                    // Courses (CRUD)
                    'create-courses',
                    'update-courses',
                    'delete-courses',

                    // User (CRUD)
                    'view-users',
                    'create-users',
                    'update-users',
                    'delete-users',

                    // Students (CRUD)
                    'create-students',
                    'delete-students',

                    // Instructors (CRUD)
                    'create-instructors',
                    'update-instructors',
                    'delete-instructors',

                    // Employees (CRUD)
                    'view-employees',
                    'create-employees',
                    'update-employees',
                    'delete-employees',

                    // User Document (CRUD)
                    'create-userDocuments',
                    'update-userDocuments',
                    'delete-userDocuments',

                    // Inventory (CRUD)
                    'create-inventories',
                    'update-inventories',
                    'delete-inventories',

                    // Inventory Category (CRUD)
                    'create-inventory-categories',
                    'update-inventory-categories',
                    'delete-inventory-categories',

                    // Inventory Supplier (CRUD)
                    'create-inventory-suppliers',
                    'update-inventory-suppliers',
                    'delete-inventory-suppliers',

                    // Year (CRUD)
                    'create-years',
                    'update-years',
                    'delete-years',

                    // Semester (CRUD)
                    'create-semesters',
                    'update-semesters',
                    'delete-semesters',

                    // Section (CRUD)
                    'create-sections',
                    'update-sections',
                    'delete-sections',

                    // Results (CRUD)
                    'create-results',
                    'update-results',
                    'delete-results',

                    // Weights (CRUD)
                    'create-weights',
                    'update-weights',
                    'delete-weights',

                    // Grades (CRUD)
                    'create-grades',
                    'update-grades',
                    'delete-grades',

                    // Curriculum (CRUD)
                    'create-curriculums',
                    'update-curriculums',
                    'delete-curriculums',

                    // Payment (CRUD)
                    'create-payments',
                    'update-payments',
                    'delete-payments',

                    // Payment Categories (CRUD)
                    'create-paymentCategories',
                    'update-paymentCategories',
                    'delete-paymentCategories',

                    // Payment Items (CRUD)
                    'create-paymentItems',
                    'update-paymentItems',
                    'delete-paymentItems',

                    // Payment Schedule (CRUD)
                    'create-paymentSchedules',
                    'update-paymentSchedules',
                    'delete-paymentSchedules',

                    // Payment Method (CRUD)
                    'create-paymentMethods',
                    'update-paymentMethods',
                    'delete-paymentMethods',

                    // Payment Type (CRUD)
                    'create-paymentTypes',
                    'update-paymentTypes',
                    'delete-paymentTypes',

                    // Assigning Relationships
                    'section-courses',
                    'attach-section-courses',
                    'detach-section-courses',

                    'section-instructors',
                    'attach-section-instructors',
                    'detach-section-instructors',

                    'attach-section-students',
                    'detach-section-students',

                    'course-instructors',
                    'attach-course-instructors',
                    'detach-course-instructors',

                    'course-students',
                    'attach-course-students',
                    'detach-course-students',

                    // User Documents
                    'update-user-document',
                    'update-user-document-image',

                    // Password Related
                    'default-password',
                    'reset-password',
                    'update-profile',

                ]);
                $role->syncPermissions($studentPermissions);
            }
            elseif (in_array($roleName, ['INSTRUCTOR'])) {
                $instructorPermissions = array_diff($permissions, [

                    // Except these permissions

                    // Permissions resource (CRUD)
                    'view-tenants',
                    'create-tenants',
                    'update-tenants',
                    'delete-tenants',

                    // Roles permissions (CRUD)
                    'view-roles',
                    'create-roles',
                    'update-roles',
                    'delete-roles',

                    // Permissions resource (CRUD)
                    'view-permissions',
                    'create-permissions',
                    'update-permissions',
                    'delete-permissions',

                    // Additional routes for roles
                    'assign-permissions-roles',
                    'attach-permissions-roles',
                    'detach-permissions-roles',

                    // Programs (CRUD)
                    'create-programs',
                    'update-programs',
                    'delete-programs',

                    // StudyMode (CRUD)
                    'create-studyModes',
                    'update-studyModes',
                    'delete-studyModes',

                    // Tracks (CRUD)
                    'create-tracks',
                    'update-tracks',
                    'delete-tracks',

                    // Courses (CRUD)
                    'create-courses',
                    'update-courses',
                    'delete-courses',

                    // User (CRUD)
                    'create-users',
                    'update-users',
                    'delete-users',

                    // Students (CRUD)
                    'create-students',
                    'update-students',
                    'delete-students',

                    // Instructors (CRUD)
                    'create-instructors',
                    'delete-instructors',

                    // Employees (CRUD)
                    'create-employees',
                    'update-employees',
                    'delete-employees',

                    // User Document (CRUD)
                    'delete-userDocuments',

                    // Inventory (CRUD)
                    'create-inventories',
                    'update-inventories',
                    'delete-inventories',

                    // Inventory Category (CRUD)
                    'create-inventory-categories',
                    'update-inventory-categories',
                    'delete-inventory-categories',

                    // Inventory Supplier (CRUD)
                    'create-inventory-suppliers',
                    'update-inventory-suppliers',
                    'delete-inventory-suppliers',

                    // Year (CRUD)
                    'create-years',
                    'update-years',
                    'delete-years',

                    // Semester (CRUD)
                    'create-semesters',
                    'update-semesters',
                    'delete-semesters',

                    // Section (CRUD)
                    'create-sections',
                    'update-sections',
                    'delete-sections',

                    // Curriculum (CRUD)
                    'create-curriculums',
                    'update-curriculums',
                    'delete-curriculums',

                    // Payment (CRUD)
                    'create-payments',
                    'update-payments',
                    'delete-payments',

                    // Payment Categories (CRUD)
                    'create-paymentCategories',
                    'update-paymentCategories',
                    'delete-paymentCategories',

                    // Payment Items (CRUD)
                    'create-paymentItems',
                    'update-paymentItems',
                    'delete-paymentItems',

                    // Payment Schedule (CRUD)
                    'create-paymentSchedules',
                    'update-paymentSchedules',
                    'delete-paymentSchedules',

                    // Payment Method (CRUD)
                    'create-paymentMethods',
                    'update-paymentMethods',
                    'delete-paymentMethods',

                    // Payment Type (CRUD)
                    'create-paymentTypes',
                    'update-paymentTypes',
                    'delete-paymentTypes',

                    // Assigning Relationships
                    'attach-section-courses',
                    'detach-section-courses',

                    'attach-section-instructors',
                    'detach-section-instructors',

                    'attach-section-students',
                    'detach-section-students',

                    'attach-course-instructors',
                    'detach-course-instructors',

                    'attach-course-students',
                    'detach-course-students',

                    // Password Related
                    'default-password',
                    'reset-password',

                ]);
                $role->syncPermissions($instructorPermissions);
            }
            elseif (in_array($roleName, ['EMPLOYEE'])) {
                $employeePermissions = array_diff($permissions, [

                    // Except these permissions

                    // Permissions resource (CRUD)
                    'view-tenants',
                    'create-tenants',
                    'update-tenants',
                    'delete-tenants',

                    // Roles permissions (CRUD)
                    'view-roles',
                    'create-roles',
                    'update-roles',
                    'delete-roles',

                    // Permissions resource (CRUD)
                    'view-permissions',
                    'create-permissions',
                    'update-permissions',
                    'delete-permissions',

                    // Additional routes for roles
                    'assign-permissions-roles',
                    'attach-permissions-roles',
                    'detach-permissions-roles',

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
                    'create-employees',
                    'delete-employees',

                    // User Document (CRUD)
                    'create-userDocuments',
                    'update-userDocuments',
                    'delete-userDocuments',

                    // Inventory (CRUD)
                    'create-inventories',
                    'update-inventories',
                    'delete-inventories',

                    // Inventory Category (CRUD)
                    'create-inventory-categories',
                    'update-inventory-categories',
                    'delete-inventory-categories',

                    // Inventory Supplier (CRUD)
                    'create-inventory-suppliers',
                    'update-inventory-suppliers',
                    'delete-inventory-suppliers',

                    // Year (CRUD)
                    'create-years',
                    'update-years',
                    'delete-years',

                    // Semester (CRUD)
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

                    // Payment Method (CRUD)
                    'view-paymentMethods',
                    'create-paymentMethods',
                    'update-paymentMethods',
                    'delete-paymentMethods',

                    // Payment Type (CRUD)
                    'view-paymentTypes',
                    'create-paymentTypes',
                    'update-paymentTypes',
                    'delete-paymentTypes',

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

                    // User Documents
                    'update-user-document',
                    'update-user-document-image',

                    // Password Related
                    'default-password',
                    'reset-password',
                    'update-password',

                ]);
                $role->syncPermissions($employeePermissions);
            }
            // Finance-Admin
            elseif (in_array($roleName, ['FINANCE-ADMIN'])) {
                $financeAdminPermissions = array_diff($permissions, [

                    // Except these permissions

                    // Permissions resource (CRUD)
                    'view-tenants',
                    'create-tenants',
                    'update-tenants',
                    'delete-tenants',

                    // Roles permissions (CRUD)
                    'view-roles',
                    'create-roles',
                    'update-roles',
                    'delete-roles',

                    // Permissions resource (CRUD)
                    'view-permissions',
                    'create-permissions',
                    'update-permissions',
                    'delete-permissions',

                    // Additional routes for roles
                    'assign-permissions-roles',
                    'attach-permissions-roles',
                    'detach-permissions-roles',

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
                    'create-users',
                    'delete-users',

                    // Students (CRUD)
                    'create-students',
                    'update-students',
                    'delete-students',

                    // Instructors (CRUD)
                    'view-instructors',
                    'create-instructors',
                    'update-instructors',
                    'delete-instructors',

                    // Employees (CRUD)
                    'create-employees',
                    'delete-employees',

                    // User Document (CRUD)
                    'create-userDocuments',
                    'update-userDocuments',
                    'delete-userDocuments',

                    // Inventory (CRUD)
                    'create-inventories',
                    'update-inventories',
                    'delete-inventories',

                    // Inventory Category (CRUD)
                    'create-inventory-categories',
                    'update-inventory-categories',
                    'delete-inventory-categories',

                    // Inventory Supplier (CRUD)
                    'create-inventory-suppliers',
                    'update-inventory-suppliers',
                    'delete-inventory-suppliers',

                    // Year (CRUD)
                    'create-years',
                    'update-years',
                    'delete-years',

                    // Semester (CRUD)
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
                    'create-curriculums',
                    'update-curriculums',
                    'delete-curriculums',

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

                    // User Documents
                    'update-user-document',
                    'update-user-document-image',

                    // Password Related
                    'default-password',
                    'reset-password',
                    'update-password',

                ]);
                $role->syncPermissions($financeAdminPermissions);
            }
            else {
                $role->syncPermissions([]);
                
            }
        }
    }
}
