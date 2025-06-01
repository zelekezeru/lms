<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('results')->insert([
            ['id' => 1, 'point' => 12, 'weight_id' => 1, 'student_id' => 1, 'instructor_id' => 1],

        ]);
    }
}
