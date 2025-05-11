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
                'name' => 'REGULAR',
            ],
            [
                'name' => 'EXTENSION',
            ],
            [
                'name' => 'DISTANCE',
            ],
            [
                'name' => 'ONLINE',
            ],

        ]);
    }
}
