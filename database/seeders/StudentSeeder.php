<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

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
                'id' => 3,
                'user_uuid' => 'SITS-0019-25',
                'name' => 'Abel Tamene Mekonnen',
                'email' => 'abel.tamene@sits.edu.et',
                'phone' => '0975228855',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => Hash::make('Abel@8855'),
                'default_password' => 'Abel@8855',
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => '2025-05-11 18:28:40',
                'updated_at' => '2025-05-11 18:28:40',
                'tenant_id' => null,
            ],
        ]);

        DB::table('students')->insert([
            [
                'id' => 1,
                'user_id' => 3,
                'id_no' => 'SITS-0019-25',
                'first_name' => 'Abel',
                'middle_name' => 'Tamene',
                'last_name' => 'Mekonnen',
                'mobile_phone' => '0975228855',
                'office_phone' => '0954756431',
                'date_of_birth' => '2000-02-02',
                'marital_status' => 'Single',
                'sex' => 'M',
                'address' => 'Weldeamanuel Avenue, Hawassa, Sidama, Ethiopia',
                'tenant_id' => null,
                'program_id' => 1,
                'track_id' => 1,
                'year_id' => 1,
                'semester_id' => 1,
                'section_id' => 2,
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

        // Generate 19 more random students
        $faker = Faker::create();
        for ($i = 2; $i <= 40; $i++) { // Changed loop to start from 2
            $user_uuid = 'SITS-' . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . mt_rand(20, 30);
            $firstName = $faker->firstName;
            $middleName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = strtolower($firstName) . '.' . strtolower($lastName) . '@sits.edu.et';
            $phone = substr($faker->phoneNumber, 0, 13); // Truncate to 15 characters
            $officePhone = substr($faker->phoneNumber, 0, 13); // Truncate to 15 characters
            $dateOfBirth = $faker->date('Y-m-d', '2005-12-31'); // Set an upper limit

            DB::table('users')->insert([
                [
                    'id' => $i + 2, // Ensure unique user IDs, offset from the initial user
                    'user_uuid' => $user_uuid,
                    'name' => $firstName . ' ' . $middleName . ' ' . $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'profile_img' => null,
                    'email_verified_at' => null,
                    'password' => Hash::make('password'), // You might want a more complex default password
                    'default_password' => 'password',
                    'password_changed' => 0,
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tenant_id' => null,
                ],
            ]);

            DB::table('students')->insert([
                [
                    'id' => $i, // Unique student ID
                    'user_id' => $i + 2, // Link to the user ID
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
                    'tenant_id' => null,
                    'program_id' => $faker->numberBetween(1, 5), // Adjust based on your program IDs
                    'track_id' => $faker->numberBetween(1, 3),   // Adjust based on your track IDs
                    'year_id' => $faker->numberBetween(1, 4),    // Adjust based on your year IDs
                    'semester_id' => $faker->numberBetween(1, 2), // Adjust based on your semester IDs
                    'section_id' => $faker->numberBetween(1, 5),  //  Adjust
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
                    'user_id' => $i+2,
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
                    'user_id' => $i + 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
