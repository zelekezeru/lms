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

        $specializations = [
            'Hermeneutics',
            'Theology',
            'Biblical Studies',
            'Church History',
            'Pastoral Ministry',
            'Ethics',
            'Spiritual Formation',
            'Missiology',
            'Leadership',
            'Apologetics',
        ];

        for ($i = 1; $i <= 7; $i++) {
            $ethiopianFirstNames = [
                'Abebe', 'Alemu', 'Bekele', 'Chala', 'Desta', 'Eshetu', 'Fikru',
                'Girma', 'Haile', 'Kebede', 'Lemma', 'Mulugeta', 'Negash', 'Samuel',
                'Tadesse', 'Yared', 'Zewdu', 'Solomon', 'Tesfaye', 'Wondimu'
            ];
            $ethiopianLastNames = [
                'Teshome', 'Gebre', 'Mengistu', 'Worku', 'Ayalew', 'Demissie', 'Gebremariam',
                'Kassahun', 'Abate', 'Asfaw', 'Belay', 'Endeshaw', 'Fekadu', 'Gizaw',
                'Hailu', 'Kassa', 'Mekonnen', 'Negussie', 'Sisay', 'Tadesse'
            ];

            $firstName = $ethiopianFirstNames[array_rand($ethiopianFirstNames)];
            $lastName = $ethiopianLastNames[array_rand($ethiopianLastNames)];
            $name = $firstName . ' ' . $lastName;

            $email = strtolower($firstName) . '.' . strtolower($lastName) . '@sits.edu.et';
            $phone = '0911' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
            $specialization = $specializations[array_rand($specializations)];

            $user = User::create([
                'user_uuid' => 'SITS-IN-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => Hash::make(strtolower($firstName) . '@' . substr($phone, -4)), // default password
                'default_password' => strtolower($firstName) . '@' . substr($phone, -4),
                'password_changed' => false,
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            Instructor::create([
                'user_id' => $user->id,
                'specialization' => $specialization,
                'employment_type' => fake()->randomElement(['FULL-TIME', 'PART-TIME', 'CONTRACT', 'VISITOR']),
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
        }

        Instructor::insert([
            [
                'id' => 9,
                'user_id' => 39,
                'specialization' => 'Counseling',
                'employment_type' => 'FULL-TIME',
                'hire_date' => '2024-01-02',
                'status' => 'Active',
                'bio' => 'Instructor at SITS',
                'created_at' => '2025-06-23 05:03:56',
                'updated_at' => '2025-06-23 05:03:56',
            ],
            [
                'id' => 10,
                'user_id' => 42,
                'specialization' => 'New Testament',
                'employment_type' => 'FULL-TIME',
                'hire_date' => '2024-01-01',
                'status' => 'Active',
                'bio' => 'Instructor at SITS',
                'created_at' => '2025-06-23 05:06:24',
                'updated_at' => '2025-06-23 05:06:24',
            ],
        ]);

        DB::table('course_instructor')->insert([
            [
                'course_id' => 1,
                'instructor_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 2,
                'instructor_id' => 9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 3,
                'instructor_id' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 4,
                'instructor_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 5,
                'instructor_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 6,
                'instructor_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 7,
                'instructor_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 8,
                'instructor_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 9,
                'instructor_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 10,
                'instructor_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 11,
                'instructor_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 12,
                'instructor_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 13,
                'instructor_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 14,
                'instructor_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 15,
                'instructor_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 16,
                'instructor_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
