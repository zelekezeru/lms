<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\TenantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Starting Seeder
        $this->call(RolePermissionSeeder::class);
        
        //After creating the roles, permissions, SuperAdmin and Tenants
        // $this->call(TenantSeeder::class);
        // $this->call(ProgramSeeder::class);
        // $this->call(DepartmentSeeder::class);
        // $this->call(StudyModeSeeder::class);
        // $this->call(CourseSeeder::class);
        // $this->call(YearSeeder::class);
        // $this->call(SemesterSeeder::class);
        // $this->call(SectionSeeder::class);
        // $this->call(StudentSeeder::class);
        // $this->call(InstructorSeeder::class);
    }
}
