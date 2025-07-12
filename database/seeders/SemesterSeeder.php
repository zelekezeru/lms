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
        for ($year = 2030, $yearId = 1; $year >= 2025; $year--, $yearId++) {
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

            // Add semester_study_mode seeding for 2025 1st semester only (semester_id = 21)
            if ($year == 2025) {
                $semesterId = DB::table('semesters')
                    ->where('name', "1st Semester of 2025")
                    ->where('year_id', $yearId)
                    ->value('id');

                if ($semesterId) {
                    $studyModes = [1, 2, 3, 4];
                    foreach ($studyModes as $i => $studyModeId) {
                        DB::table('semester_study_mode')->insert([
                            'semester_id' => $semesterId,
                            'study_mode_id' => $studyModeId,
                            'start_date' => now()->subMonths(2)->startOfMonth(),
                            'end_date' => now()->addMonths(2)->endOfMonth(),
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
