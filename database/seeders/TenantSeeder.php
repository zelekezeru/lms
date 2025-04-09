<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the user exists before inserting the tenant
        DB::table('users')->insertOrIgnore([
            'id' => 2,
            'name' => 'Endale Sebsibe',
            'email' => 'esebsibe@gmail.com',
            'password' => Hash::make('4565@lms'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('tenants')->insert([
            'id' => 1,
            'name' => 'SITS',
            'email' => 'sitsethiopia@gmail.com',
            'code' => 'Tenant/0001/25',
            'phone' => '0911274565',
            'address' => 'Weldeamanuel Avenue, Hawassa, Sidama, Ethiopia',
            'logo' => 'logo/B532heVzz5Azgj7RzfBmP3UhgYT0bD4nExcxBSWH.png',
            'contact_person' => 'Endale Sebsibe',
            'contact_phone' => '0975210098',
            'contact_email' => 'esebsibe@gmail.com',
            'aggrement' => '',
            'default_password' => '4565@lms',
            'user_id' => 2,
            'status' => 1,
            'allowed' => 1,
            'paid' => 1,
            'password' => '$2y$12$AWt5c9Tzfpr5hEFRihd8Beee5Vb/de3IGv3vlVxw41AihrIOOpodu',
            'password_changed' => 0,
            'created_at' => '2025-03-16 05:02:43',
            'updated_at' => '2025-03-18 15:53:34',
        ]);
    }
}