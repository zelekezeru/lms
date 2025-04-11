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
            'name' => 'Super Admin',
            'user_uuid' => 'SA-25-001',
            'email' => 'sadmin@gmail.com',
            'password' => bcrypt('123456789'), // Use a secure password
            'created_at' => now(),
            'updated_at' => now(),
        ],
            [
            'id' => 2,
            'name' => 'Admin User',
            'user_uuid' => 'AD-25-001',
            'email' => 'admin@example.com',
            'password' => bcrypt('123456789'), // Use a secure password
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }
}