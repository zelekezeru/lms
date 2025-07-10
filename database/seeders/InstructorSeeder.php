<?php

namespace Database\Seeders;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create one random instructor with full information

        $user = User::create([
            
            'user_uuid' => 'SITS-IN-001',
            'name' => 'Instructor User Test',
            'email' => 'instructor.user@sits.edu.et',
            'phone' => '0911000000',
            'profile_img' => null,
            'email_verified_at' => null,
            'password' => Hash::make('instructor@0000'), // default password
            'default_password' => 'instructor@0000',
            'password_changed' => false,
            'tenant_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        Instructor::create([
            'user_id' => $user->id,
            'specialization' => 'Hermeneutics',
            'employment_type' => 'FULL-TIME',
            'hire_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'status' => 'Active',
            'bio' => fake()->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Assign the SUPER-ADMIN role to the user
        DB::table('model_has_roles')->insert([
            [
                'role_id' => 7, // SUPER-ADMIN role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => $user->id, // The user ID
            ],
        ]);

        // Assign instructor 1 to all courses (assuming course IDs from 1 to 16)
        $courseInstructorData = [];
        for ($courseId = 1; $courseId <= 56; $courseId++) {
            $courseInstructorData[] = [
            'course_id' => $courseId,
            'instructor_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            ];
        }
        DB::table('course_instructor')->insert($courseInstructorData);
    }
}
