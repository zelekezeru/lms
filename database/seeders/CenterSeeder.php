<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('centers')->insert([
            
        ]);

        // Add 4 more centers
        DB::table('centers')->insert([
            [
                'id' => 1,
                'name' => 'Hawassa Center',
                'address' => 'Hawassa, Sidama, Ethiopia',
                'code' => 'SITS-C-001',
                'status' => 'Active',
                'created_at' => '2025-06-11 18:04:48',
                'updated_at' => '2025-06-11 18:04:48',
            ],
            [
                'id' => 2,
                'name' => 'Adama Center',
                'address' => 'Adama, Oromia, Ethiopia',
                'code' => 'SITS-C-002',
                'status' => 'Active',
                'created_at' => '2025-06-12 10:00:00',
                'updated_at' => '2025-06-12 10:00:00',
            ],
            [
                'id' => 3,
                'name' => 'Bahir Dar Center',
                'address' => 'Bahir Dar, Amhara, Ethiopia',
                'code' => 'SITS-C-003',
                'status' => 'Active',
                'created_at' => '2025-06-12 10:05:00',
                'updated_at' => '2025-06-12 10:05:00',
            ],
            [
                'id' => 4,
                'name' => 'Dire Dawa Center',
                'address' => 'Dire Dawa, Ethiopia',
                'code' => 'SITS-C-004',
                'status' => 'Active',
                'created_at' => '2025-06-12 10:10:00',
                'updated_at' => '2025-06-12 10:10:00',
            ],
            [
                'id' => 5,
                'name' => 'Mekelle Center',
                'address' => 'Mekelle, Tigray, Ethiopia',
                'code' => 'SITS-C-005',
                'status' => 'Active',
                'created_at' => '2025-06-12 10:15:00',
                'updated_at' => '2025-06-12 10:15:00',
            ],
        ]);

        DB::table('users')->insert([
            [
                'id' => 34,
                'user_uuid' => 'SITS-CO-0001',
                'name' => 'Adebe Melese Tadese',
                'email' => 'abebe.tadese@sits.edu.et',
                'phone' => '0915161817',
                'profile_img' => 'profile_images/bNeyHSuct7fFxNPF0meZvhYp9hHqT2YdYleD3DPm.jpg',
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'tenant_id' => 1,
            ],
            [
                'id' => 35,
                'user_uuid' => 'SITS-CO-0002',
                'name' => 'Tigist Alemu',
                'email' => 'tigist.alemu@sits.edu.et',
                'phone' => '0912345671',
                'profile_img' => 'profile_images/tigist.jpg',
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'tenant_id' => 1,
            ],
            [
                'id' => 36,
                'user_uuid' => 'SITS-CO-0003',
                'name' => 'Mulugeta Bekele',
                'email' => 'mulugeta.bekele@sits.edu.et',
                'phone' => '0912345672',
                'profile_img' => 'profile_images/mulugeta.jpg',
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'tenant_id' => 1,
            ],
            [
                'id' => 37,
                'user_uuid' => 'SITS-CO-0004',
                'name' => 'Hirut Tesfaye',
                'email' => 'hirut.tesfaye@sits.edu.et',
                'phone' => '0912345673',
                'profile_img' => 'profile_images/hirut.jpg',
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'tenant_id' => 1,
            ],
            [
                'id' => 38,
                'user_uuid' => 'SITS-CO-0005',
                'name' => 'Samuel Gebre',
                'email' => 'samuel.gebre@sits.edu.et',
                'phone' => '0912345674',
                'profile_img' => 'profile_images/samuel.jpg',
                'email_verified_at' => null,
                'password' => '$2y$12$wp5F46QHQShXx82FbZB5w.wF4UPcUGT6tTnBsgwvX4neH29h0N0Ue',
                'default_password' => null,
                'password_changed' => 0,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'tenant_id' => 1,
            ],
        ]);

        // Add coordinators for the new centers
        DB::table('coordinators')->insert([
            [
                'id' => 1,
                'center_id' => 1,
                'user_id' => 34,
                'created_at' => '2025-06-11 18:05:14',
                'updated_at' => '2025-06-11 18:05:14',
            ],
            [
                'id' => 2,
                'center_id' => 2,
                'user_id' => 35,
                'created_at' => '2025-06-12 10:00:10',
                'updated_at' => '2025-06-12 10:00:10',
            ],
            [
                'id' => 3,
                'center_id' => 3,
                'user_id' => 36,
                'created_at' => '2025-06-12 10:05:10',
                'updated_at' => '2025-06-12 10:05:10',
            ],
            [
                'id' => 4,
                'center_id' => 4,
                'user_id' => 37,
                'created_at' => '2025-06-12 10:10:10',
                'updated_at' => '2025-06-12 10:10:10',
            ],
            [
                'id' => 5,
                'center_id' => 5,
                'user_id' => 38,
                'created_at' => '2025-06-12 10:15:10',
                'updated_at' => '2025-06-12 10:15:10',
            ],
        ]);

        // Assign roles to each user
        $userRoles = [
            34 => 7, // Adebe Melese Tadese - SUPER-ADMIN
            35 => 7, // Tigist Alemu - COORDINATOR
            36 => 7, // Mulugeta Bekele - COORDINATOR
            37 => 7, // Hirut Tesfaye - COORDINATOR
            38 => 7, // Samuel Gebre - COORDINATOR
        ];
        foreach ($userRoles as $userId => $roleId) {
            DB::table('model_has_roles')->insert([
            [
                'role_id' => $roleId,
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ],
            ]);
        }

    }
}
