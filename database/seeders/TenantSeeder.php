<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tenants')->insert([
            'id' => 1, 'name' => 'SITS', 'email' => 'sitsethiopia@gmail.com', 'code' => 'Tenant/0001/25',
            'phone' => '0911274565', 'address' => 'Weldeamanuel Avenue, Hawassa, Sidama, Ethiopia',
            'contact_person' => 'Endale Sebsibe', 'contact_phone' => '0975210097', 'aggrement' => null,
            'user_id' => 1, 'status' => 1, 'allowed' => 1, 'paid' => 1, 'password' => 'tenant@SITS',
            'password_changed' => 0, 'created_at' => '2025-03-16 08:02:43', 'updated_at' => '2025-03-16 08:02:43',
        ]);
    }
}