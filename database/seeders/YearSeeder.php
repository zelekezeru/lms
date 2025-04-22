<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('years')->insert([
            
            [
                'id' => 1,
                'name' => '2025',
                'status' => 'Active',
                'is_approved' => 1,
                'is_completed' => 0,
                'created_at' => '2025-04-15 05:49:22',
                'updated_at' => '2025-04-15 05:49:22',
            ],
            [
                'id' => 2,
                'name' => '2024',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => '2025-04-15 05:49:06',
                'updated_at' => '2025-04-15 05:49:06',
            ],
            [
                'id' => 3,
                'name' => '2023',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => '2025-04-15 05:47:47',
                'updated_at' => '2025-04-15 05:48:04',
            ],
            [
                'id' => 4,
                'name' => '2022',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => '2025-04-15 05:45:39',
                'updated_at' => '2025-04-15 05:47:28',
            ],
            [
                'id' => 5,
                'name' => '2021',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => '2025-04-15 05:45:01',
                'updated_at' => '2025-04-15 05:45:01',
            ],
            [
                'id' => 6,
                'name' => '2020',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => '2025-04-15 05:35:53',
                'updated_at' => '2025-04-15 05:35:53',
            ],
            [
                'id' => 7,
                'name' => '2019',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => '2025-04-15 05:35:29',
                'updated_at' => '2025-04-15 05:35:29',
            ],
            [
                'id' => 8,
                'name' => '2018',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => '2025-04-15 05:34:58',
                'updated_at' => '2025-04-15 05:34:58',
            ],
            [
                'id' => 9,
                'name' => '2017',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => '2025-04-15 05:34:28',
                'updated_at' => '2025-04-15 05:34:28',
            ],
            [
                'id' => 10,
                'name' => '2016',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
                'created_at' => null,
                'updated_at' => null,
            ],

        ]);
    }
}
