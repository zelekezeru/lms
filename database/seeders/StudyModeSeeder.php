<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudyModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('study_modes')->insert([
            [
                'id' => 1,
                'name' => 'REGULAR',
                'no_of_semesters' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'EXTENSION',
                'no_of_semesters' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'ONLINE',
                'no_of_semesters' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'DISTANCE',
                'no_of_semesters' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
