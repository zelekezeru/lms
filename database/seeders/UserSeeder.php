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
            'id' => 2,
            'user_uuid' => 'ADMIN/0001/Alfa/25',
            'name' => 'Endale Sebsibe',
            'email' => 'esebsibe@gmail.com',
            'tenant_id' => 1,
            'profile_img' => null,
            'email_verified_at' => null,
            'password' => '$2y$12$82SK3Qnxk403XI.zkyhW9uAGKJnJmbncCH62rkNAn4CJcKdDhPqGS',
            'password_changed' => 0,
            'remember_token' => null,
            'created_at' => '2025-03-18 12:18:14',
            'updated_at' => '2025-03-18 15:50:46',
        ]);
    }
}