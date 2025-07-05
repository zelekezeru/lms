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
        for ($year = 2020, $yearId = 6; $year <= 2025; $year++, $yearId--) {
            DB::table('semesters')->insert([
                [
                    'name' => "1st Semester of $year",
                    'year_id' => $yearId,
                    'level' => 1,
                    'start_date' => "$year-01-01",
                    'end_date' => "$year-03-31",
                    'status' => $year === 2025 ? 'Active' : 'Inactive',
                    'is_approved' => 1,
                    'is_completed' => $year < 2025 ? 1 : 0,
                ],
                [
                    'name' => "2nd Semester of $year",
                    'year_id' => $yearId,
                    'level' => 2,
                    'start_date' => "$year-04-01",
                    'end_date' => "$year-06-30",
                    'status' => 'Inactive',
                    'is_approved' => 1,
                    'is_completed' => $year < 2025 ? 1 : 0,
                ],
                [
                    'name' => "3rd Semester of $year",
                    'year_id' => $yearId,
                    'level' => 3,
                    'start_date' => "$year-07-01",
                    'end_date' => "$year-09-30",
                    'status' => 'Inactive',
                    'is_approved' => 1,
                    'is_completed' => $year < 2025 ? 1 : 0,
                ],
                [
                    'name' => "4th Semester of $year",
                    'year_id' => $yearId,
                    'level' => 4,
                    'start_date' => "$year-10-01",
                    'end_date' => "$year-12-31",
                    'status' => 'Inactive',
                    'is_approved' => 1,
                    'is_completed' => $year < 2025 ? 1 : 0,
                ],
            ]);
        }
    }
}
