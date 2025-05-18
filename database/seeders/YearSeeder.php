<?php

namespace Database\Seeders;

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
        ]);
    }
}
