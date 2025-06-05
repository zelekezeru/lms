<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the initial student (already in the original code)
        DB::table('users')->insert([
            [
                'id' => 6,
                'user_uuid' => 'SITS-0001-25',
                'name' => 'Student User Test',
                'email' => 'student.user@sits.edu.et',
                'phone' => '0975000000',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => Hash::make('student@0000'),
                'default_password' => 'student@0000',
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => '2025-05-11 18:28:40',
                'updated_at' => '2025-05-11 18:28:40',
                'tenant_id' => 1,
            ],
        ]);

        DB::table('students')->insert([
            [
                'id' => 1,
                'user_id' => 6,
                'id_no' => 'SITS-0001-25',
                'first_name' => 'Student',
                'middle_name' => 'User',
                'last_name' => 'Test',
                'mobile_phone' => '0975000000',
                'office_phone' => '0462216431',
                'date_of_birth' => '2000-02-02',
                'marital_status' => 'Single',
                'sex' => 'M',
                'address' => 'Weldeamanuel Avenue, Hawassa, Sidama, Ethiopia',
                'tenant_id' => 1,
                'program_id' => 1,
                'track_id' => 1,
                'study_mode_id' => 1,
                'year_id' => 1,
                'semester_id' => 1,
                'section_id' => 1,
                'created_at' => '2025-05-11 18:28:40',
                'updated_at' => '2025-05-11 18:28:50',
            ],
        ]);

        DB::table('statuses')->insert([
            [
                'id' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
                'is_approved' => 0,
                'is_completed' => 0,
                'is_verified' => 0,
                'is_enrolled' => 0,
                'is_graduated' => 0,
                'is_scholarship' => 0,
                'is_scholarship_approved' => 0,
                'is_scholarship_verified' => 0,
                'created_by_name' => 'S Admin User',
                'updated_by_name' => null,
                'deleted_by_name' => null,
                'approved_by_name' => null,
                'completed_by_name' => null,
                'verified_by_name' => null,
                'enrolled_by_name' => null,
                'graduated_by_name' => null,
                'scholarship_by_name' => null,
                'scholarship_approved_by_name' => null,
                'scholarship_verified_by_name' => null,
                'deleted_at' => null,
                'approved_at' => null,
                'completed_at' => null,
                'verified_at' => null,
                'enrolled_at' => null,
                'graduated_at' => null,
                'scholarship_at' => null,
                'scholarship_approved_at' => null,
                'scholarship_verified_at' => null,
                'student_id' => 1,
                'user_id' => 3,
                'created_at' => '2025-05-11 18:28:40',
                'updated_at' => '2025-05-11 18:29:08',
            ],
        ]);

        DB::table('churches')->insert([
            [
                'id' => 1,
                'pastor_name' => 'Asfawu Ashagre',
                'pastor_phone' => '0958856854',
                'church_name' => 'MKC',
                'church_address' => 'Hawassa, Sidama',
                'position_denomination' => 'Evangelist',
                'student_id' => 1,
                'user_id' => 3,
                'created_at' => '2025-05-11 18:28:40',
                'updated_at' => '2025-05-11 18:28:40',
            ],
        ]);

        // Assign the SUPER-ADMIN role to the user
        DB::table('model_has_roles')->insert([
            [
                'role_id' => 8, // SUPER-ADMIN role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 3, // The user ID
            ],
        ]);

        // Generate 19 more random students
        $faker = Faker::create();
        for ($i = 2; $i <= 20; $i++) { // Changed loop to start from 2
            $user_uuid = 'SITS-' . str_pad($i, 4, '0', STR_PAD_LEFT) . '-25';
            $firstName = $faker->firstName;
            $middleName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = strtolower($firstName) . '.' . strtolower($lastName) . '@sits.edu.et';
            $phone = '0916' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT); // Generate a random phone number
            $officePhone = '0462' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
            $dateOfBirth = $faker->date('Y-m-d', '2005-12-31'); // Set an upper limit

            DB::table('users')->insert([
                [
                    'id' => $i + 5, // Ensure unique user IDs, offset from the initial user
                    'user_uuid' => $user_uuid,
                    'name' => $firstName . ' ' . $middleName . ' ' . $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'profile_img' => null,
                    'email_verified_at' => null,
                    'password' => Hash::make(strtolower($firstName) . '@' . substr($phone, -4)), // Default password
                    'default_password' => strtolower($firstName) . '@' . substr($phone, -4), // Example: Abel@8855
                    'password_changed' => 0,
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tenant_id' => 1,
                ],
            ]);

            DB::table('students')->insert([
                [
                    'id' => $i, // Unique student ID
                    'user_id' => $i + 5, // Link to the user ID
                    'id_no' => $user_uuid,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'mobile_phone' => $phone,
                    'office_phone' => $officePhone, // Truncated office phone
                    'date_of_birth' => $dateOfBirth,
                    'marital_status' => $faker->randomElement(['Single', 'Married', 'Divorced']),
                    'sex' => $faker->randomElement(['M', 'F']),
                    'address' => $faker->address,
                    'tenant_id' => 1,
                    'program_id' => $faker->numberBetween(1, 5), // Adjust based on your program IDs
                    'track_id' => $faker->numberBetween(1, 3),   // Adjust based on your track IDs
                    'study_mode_id' => 1, // Adjust based on your study mode IDs
                    'year_id' => 1,    // Adjust based on your year IDs
                    'semester_id' => $faker->numberBetween(1, 2), // Adjust based on your semester IDs
                    'section_id' => 1,  //  Adjust
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            DB::table('statuses')->insert([
                [
                    'id' => $i,
                    'is_active' => 1,
                    'is_deleted' => 0,
                    'is_approved' => 0,
                    'is_completed' => 0,
                    'is_verified' => 0,
                    'is_enrolled' => 0,
                    'is_graduated' => 0,
                    'is_scholarship' => 0,
                    'is_scholarship_approved' => 0,
                    'is_scholarship_verified' => 0,
                    'created_by_name' => 'S Admin User',
                    'updated_by_name' => null,
                    'deleted_by_name' => null,
                    'approved_by_name' => null,
                    'completed_by_name' => null,
                    'verified_by_name' => null,
                    'enrolled_by_name' => null,
                    'graduated_by_name' => null,
                    'scholarship_by_name' => null,
                    'scholarship_approved_by_name' => null,
                    'scholarship_verified_by_name' => null,
                    'deleted_at' => null,
                    'approved_at' => null,
                    'completed_at' => null,
                    'verified_at' => null,
                    'enrolled_at' => null,
                    'graduated_at' => null,
                    'scholarship_at' => null,
                    'scholarship_approved_at' => null,
                    'scholarship_verified_at' => null,
                    'student_id' => $i,
                    'user_id' => $i + 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            DB::table('churches')->insert([
                [
                    'id' => $i,
                    'pastor_name' => $faker->name,
                    'pastor_phone' => substr($faker->phoneNumber, 0, 15), // Truncated pastor phone
                    'church_name' => $faker->company,
                    'church_address' => $faker->address,
                    'position_denomination' => $faker->jobTitle,
                    'student_id' => $i,
                    'user_id' => $i + 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // Assign the SUPER-ADMIN role to the user
            DB::table('model_has_roles')->insert([
                [
                    'role_id' => 8, // SUPER-ADMIN role ID
                    'model_type' => 'App\Models\User', // The model type for the user
                    'model_id' => $i + 2, // The user ID
                ],
            ]);
        }
    }
}
