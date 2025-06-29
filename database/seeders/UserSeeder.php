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
                'is_deleted' => 0,
                'active_role_id' => null,
                'deleted_at' => null,
                'deleted_by_name' => null,
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
                'is_deleted' => 0,
                'active_role_id' => null,
                'deleted_at' => null,
                'deleted_by_name' => null,
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'tenant_id' => 1,
            ],
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
                'is_deleted' => 0,
                'active_role_id' => null,
                'deleted_at' => null,
                'deleted_by_name' => null,
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'tenant_id' => 1,
            ],
            [
                'id' => 39,
                'user_uuid' => 'SITS-IN-010',
                'name' => 'Abenezer Ayalew Mekonnen',
                'email' => 'abenezer.ayalew@sits.edu.et',
                'phone' => '0926493321',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$XE4oFJqgVUqWSdK.CCDYR.HoiSmZwj8hlSZ8BGSkBH2pguN6GcbS6',
                'default_password' => 'abenezer@3321',
                'password_changed' => 0,
                'is_deleted' => 0,
                'active_role_id' => 7,
                'deleted_at' => null,
                'deleted_by_name' => null,
                'remember_token' => 'eQLW1paCm02S2Hcw7fpChSWZtj0RZgS6kWB2PRDvPjDgXQfQvcGEYIDteY26',
                'created_at' => '2025-06-23 05:03:56',
                'updated_at' => '2025-06-23 07:37:22',
                'tenant_id' => 1,
            ],
            [
                'id' => 41,
                'user_uuid' => 'SITS-EM-003',
                'name' => 'Misganu Petros Heramo',
                'email' => 'misganu.petros@sits.edu.et',
                'phone' => '0937216471',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$.UrN1ltrUs0UvdKC2l8FXewmNQcm1TE2vl5MZJ6/dg5U5NlFRDkeC',
                'default_password' => 'misganu@6471',
                'password_changed' => 0,
                'is_deleted' => 0,
                'active_role_id' => null,
                'deleted_at' => null,
                'deleted_by_name' => null,
                'remember_token' => null,
                'created_at' => '2025-06-23 05:04:33',
                'updated_at' => '2025-06-23 05:04:33',
                'tenant_id' => 1,
            ],
            [
                'id' => 42,
                'user_uuid' => 'SITS-IN-011',
                'name' => 'Tamiru Lijalem Yigezu',
                'email' => 'tamiru.lijalem@sits.edu.et',
                'phone' => '0910921472',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$EGlmDqlBO8KpZ.1OnmPo8OSfiAIf5FxEz3hiIJg/6F2I5eSGVCkb6',
                'default_password' => null,
                'password_changed' => 0,
                'is_deleted' => 0,
                'active_role_id' => 7,
                'deleted_at' => null,
                'deleted_by_name' => null,
                'remember_token' => 'Kk6bQa1LgSqiWHQYfnxn0aUQittRgmFNPaHt5uWeHcA2pTeogrO0eezJWWY0',
                'created_at' => '2025-06-23 05:06:24',
                'updated_at' => '2025-06-25 04:26:30',
                'tenant_id' => 1,
            ],
            [
                'id' => 43,
                'user_uuid' => 'SITS-0021-25',
                'name' => 'Abelk Tamene Mekonnen',
                'email' => 'abelk.tamene@sits.edu.et',
                'phone' => '0975228855',
                'profile_img' => 'profile_images/profile_abelktamenemekonnen.webp',
                'email_verified_at' => null,
                'password' => '$2y$12$oWjLZeCqMJkCHu0ah6N0..ZyYTInHz57uzaFfna7nSqa0.xiQll2i',
                'default_password' => 'abelk@8855',
                'password_changed' => 0,
                'is_deleted' => 0,
                'active_role_id' => null,
                'deleted_at' => null,
                'deleted_by_name' => null,
                'remember_token' => null,
                'created_at' => '2025-06-23 06:40:35',
                'updated_at' => '2025-06-29 13:34:35',
                'tenant_id' => null,
            ],
            [
                'id' => 44,
                'user_uuid' => 'SITS-EM-004',
                'name' => 'Kalkidan Eshetu Endale',
                'email' => 'kalkidan.eshetu@sits.edu.et',
                'phone' => '0916029589',
                'profile_img' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$Kv01D0AYyAzDFCFt0jOXvOEdX4og8WPR2ofBHdXKJ91gZo8GF4YC.',
                'default_password' => null,
                'password_changed' => 0,
                'is_deleted' => 0,
                'active_role_id' => 13,
                'deleted_at' => null,
                'deleted_by_name' => null,
                'remember_token' => 'gks4p5qfyeYO8tuIeTbGkEZJUsGvBWBxrHzh0UMxt4eW6qmNrPI26BblNOQU',
                'created_at' => '2025-06-24 06:11:57',
                'updated_at' => '2025-06-24 07:19:48',
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
            [
                'role_id' => 6, // SUPER-ADMIN role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 41, // The user ID
            ],
            [
                'role_id' => 7, // SUPER-ADMIN role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 39, // The user ID
            ],
            [
                'role_id' => 7, // SUPER-ADMIN role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 42, // The user ID
            ],
            [
                'role_id' => 7, // SUPER-ADMIN role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 43, // The user ID
            ],
            [
                'role_id' => 13, // FINANCE role ID
                'model_type' => 'App\Models\User', // The model type for the user
                'model_id' => 44, // The user ID
            ],
        ]);
    }
}
