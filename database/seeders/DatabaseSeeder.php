<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Starting Seeder
        $this->call(RolePermissionSeeder::class);

        // After creating the roles, permissions, SuperAdmin
        $this->call(TenantSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(StudyModeSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(TrackSeeder::class);
        $this->call(YearSeeder::class);
        $this->call(SemesterSeeder::class);
        $this->call(SectionSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(InstructorSeeder::class);
        $this->call(PaymentCategorySeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(WeightSeeder::class);
        $this->call(CurriculumSeeder::class);
        $this->call(CourseOfferingSeeder::class);
        $this->call(RoomSeeder::class);
        // $this->call(CenterSeeder::class);
    }
}
