<?php

namespace Database\Seeders;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            'Hermeneutics', 'Theology', 'Biblical Studies', 'Church History',
            'Pastoral Ministry', 'Ethics', 'Spiritual Formation', 'Missiology',
            'Leadership', 'Apologetics',
        ];

        for ($i = 1; $i <= 8; $i++) {
            $name = fake()->name;
            $email = fake()->unique()->safeEmail;
            $phone = fake()->phoneNumber;
            $specialization = $specializations[array_rand($specializations)];

            $user = User::create([
                'user_uuid' => 'SITS-IN-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => Hash::make('password'), // default password
                'default_password' => null,
                'password_changed' => false,
                'tenant_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Instructor::create([
                'user_id' => $user->id,
                'specialization' => $specialization,
                'employment_type' => fake()->randomElement(['FULL-TIME', 'PART-TIME', 'CONTRACT', 'Visitor']),
                'hire_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                'status' => fake()->randomElement(['Active', 'Inactive']),
                'bio' => fake()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
