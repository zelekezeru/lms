<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
                'phone' => '0911000000',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'tenant_id' => 1,
            ],
            [
                'id' => 3,
                'user_uuid' => 'SITS-EM-001',
                'name' => 'Registrar User',
                'email' => 'registrar.user@sits.edu.et',
                'phone' => '0906000000',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'tenant_id' => 1,
            ],
            // Finance User
            [
                'id' => 4,
                'user_uuid' => 'SITS-EM-002',
                'name' => 'Finance User',
                'email' => 'finance.user@sits.edu.et',
                'phone' => '0911000001',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
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
            // Assign the REGISTRAR role to the registrar user
            [
                'role_id' => 6, // REGISTERAR role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 3, // The user ID
            ],
            [
                'role_id' => 13, // FINANCE role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 4, // The user ID
            ],
        ]);
    }
}
