<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('weights')->insert([
            [
                'name' => 'Assignment 1',
                'point' => '20',
                'description' => 'First assignment of the semester',
                'instructor_id' => 1,
                'section_id' => 1,
                'semester_id' => 1,
                'course_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Midterm Exam',
                'point' => '30',
                'description' => 'Midterm examination',
                'instructor_id' => 1,
                'section_id' => 1,
                'semester_id' => 1,
                'course_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Final Exam',
                'point' => '50',
                'description' => 'Final examination of the semester',
                'instructor_id' => 1,
                'section_id' => 1,
                'semester_id' => 1,
                'course_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
