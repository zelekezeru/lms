<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'user_uuid' => 'SA/25',
                'name' => 'S Admin User',
                'email' => 'admin@gmail.com',
                'phone' => '0911121314',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => '2025-04-23 18:46:47',
                'updated_at' => '2025-04-24 07:44:17',
                'tenant_id' => 1,
            ],
        ]);

         // Assign the SUPER-ADMIN role to the user
         DB::table('model_has_roles')->insert([
            [
                'role_id' => 1, // SUPER-ADMIN role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 1, // The user ID
            ],
        ]);
    }
}