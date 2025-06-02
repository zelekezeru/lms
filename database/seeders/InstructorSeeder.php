<?php

namespace Database\Seeders;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Instructor::create([
            'user_id' => 23,
            'specialization' => 'Hermeneutics',
            'employment_type' => 'FULL-TIME',
            'hire_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'status' => 'Active',
            'bio' => fake()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign the SUPER-ADMIN role to the user
        DB::table('model_has_roles')->insert([
            [
                'role_id' => 7, // SUPER-ADMIN role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => $user->id, // The user ID
            ],
        ]);

        $specializations = [
            'Hermeneutics', 'Theology', 'Biblical Studies', 'Church History',
            'Pastoral Ministry', 'Ethics', 'Spiritual Formation', 'Missiology',
            'Leadership', 'Apologetics',
        ];

        for ($i = 1; $i <= 7; $i++) {
            $name = fake()->name;
            $firstName = explode(' ', $name)[0]; // Extract the first name
            $lastName = explode(' ', $name)[1] ?? ''; // Extract the last name (if available)
            $email = strtolower($firstName).'.'.strtolower($lastName).'@sits.edu.et';
            $phone = '0911'.str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT); 
            $specialization = $specializations[array_rand($specializations)];

            $user = User::create([
                'user_uuid' => 'SITS-IN-'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => Hash::make(strToLower($firstName).'@'.substr($phone, -4)), // default password
                'default_password' => strToLower($firstName).'@'.substr($phone, -4),
                'password_changed' => false,
                'tenant_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Instructor::create([
                'user_id' => $user->id,
                'specialization' => $specialization,
                'employment_type' => fake()->randomElement(['FULL-TIME', 'PART-TIME', 'CONTRACT', 'VISITOR']),
                'hire_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                'status' => 'Active',
                'bio' => fake()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign the SUPER-ADMIN role to the user
            DB::table('model_has_roles')->insert([
                [
                    'role_id' => 7, // SUPER-ADMIN role ID
                    'model_type' => 'App\Models\User', // The model type for the user
                    'model_id' => $user->id, // The user ID
                ],
            ]);
        }
    }
}
