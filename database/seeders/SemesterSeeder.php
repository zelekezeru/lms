<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('semesters')->insert([
            [
                'name' => '1st - 2025',
                'year_id' => 1,
                'level' => 1,
                'start_date' => '2025-01-10',
                'end_date' => '2025-04-10',
                'status' => 'Active',
                'is_approved' => 1,
                'is_completed' => 0,
            ],
            [
                'name' => '2nd - 2025',
                'year_id' => 1,
                'level' => 2,
                'start_date' => '2025-04-15',
                'end_date' => '2025-07-15',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 0,
            ],
            [
                'name' => 'Rainy Season - 2025',
                'year_id' => 1,
                'level' => 3,
                'start_date' => '2025-08-01',
                'end_date' => '2025-10-31',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 0,
            ],

        ]);
    }
}
