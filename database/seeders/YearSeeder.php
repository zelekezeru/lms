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
        $years = [
            ['name' => '2030', 'status' => 'Inactive', 'is_approved' => 1, 'is_completed' => 0],
            ['name' => '2029', 'status' => 'Inactive', 'is_approved' => 1, 'is_completed' => 0],
            ['name' => '2028', 'status' => 'Inactive', 'is_approved' => 1, 'is_completed' => 0],
            ['name' => '2027', 'status' => 'Inactive', 'is_approved' => 0, 'is_completed' => 0],
            ['name' => '2026', 'status' => 'Inactive', 'is_approved' => 0, 'is_completed' => 0],
            ['name' => '2025', 'status' => 'Active', 'is_approved' => 0, 'is_completed' => 0],
        ];

        $now = Carbon::now();
        foreach ($years as $index => $year) {
            DB::table('years')->insert([
                'id' => $index + 1,
                'name' => $year['name'],
                'status' => $year['status'],
                'is_approved' => $year['is_approved'],
                'is_completed' => $year['is_completed'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
