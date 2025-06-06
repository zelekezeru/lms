<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
                'is_approved' => 0,
                'is_completed' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => '2024',
                'status' => 'Inactive',
                'is_approved' => 0,
                'is_completed' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'id' => 3,
                'name' => '2023',
                'status' => 'Inactive',
                'is_approved' => 0,
                'is_completed' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'name' => '2022',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => '2021',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'name' => '2020',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
        ]);
    }
}
