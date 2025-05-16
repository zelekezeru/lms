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
            ['id' => 2, 'point' => 19, 'weight_id' => 1, 'student_id' => 2, 'instructor_id' => 1],
            ['id' => 3, 'point' => 15, 'weight_id' => 1, 'student_id' => 3, 'instructor_id' => 1],
            ['id' => 4, 'point' => 15, 'weight_id' => 1, 'student_id' => 4, 'instructor_id' => 1],
            ['id' => 5, 'point' => 19, 'weight_id' => 1, 'student_id' => 5, 'instructor_id' => 1],
            ['id' => 6, 'point' => 14, 'weight_id' => 1, 'student_id' => 6, 'instructor_id' => 1],
            ['id' => 7, 'point' => 6,  'weight_id' => 1, 'student_id' => 7, 'instructor_id' => 1],
            ['id' => 8, 'point' => 12, 'weight_id' => 1, 'student_id' => 8, 'instructor_id' => 1],
            ['id' => 9, 'point' => 19, 'weight_id' => 1, 'student_id' => 9, 'instructor_id' => 1],
            ['id' => 10, 'point' => 18, 'weight_id' => 1, 'student_id' => 10, 'instructor_id' => 1],
            ['id' => 11, 'point' => 16, 'weight_id' => 1, 'student_id' => 11, 'instructor_id' => 1],
            ['id' => 12, 'point' => 10, 'weight_id' => 1, 'student_id' => 12, 'instructor_id' => 1],
            ['id' => 13, 'point' => 19, 'weight_id' => 1, 'student_id' => 13, 'instructor_id' => 1],
            ['id' => 14, 'point' => 15, 'weight_id' => 1, 'student_id' => 14, 'instructor_id' => 1],
            ['id' => 15, 'point' => 15, 'weight_id' => 1, 'student_id' => 15, 'instructor_id' => 1],
            ['id' => 16, 'point' => 19, 'weight_id' => 1, 'student_id' => 16, 'instructor_id' => 1],
            ['id' => 17, 'point' => 16, 'weight_id' => 1, 'student_id' => 17, 'instructor_id' => 1],
            ['id' => 18, 'point' => 16, 'weight_id' => 1, 'student_id' => 18, 'instructor_id' => 1],
            ['id' => 19, 'point' => 14, 'weight_id' => 1, 'student_id' => 19, 'instructor_id' => 1],
            ['id' => 20, 'point' => 18, 'weight_id' => 1, 'student_id' => 20, 'instructor_id' => 1],
            ['id' => 21, 'point' => 25, 'weight_id' => 2, 'student_id' => 1, 'instructor_id' => 1],
            ['id' => 22, 'point' => 23, 'weight_id' => 2, 'student_id' => 2, 'instructor_id' => 1],
            ['id' => 23, 'point' => 28, 'weight_id' => 2, 'student_id' => 3, 'instructor_id' => 1],
            ['id' => 24, 'point' => 15, 'weight_id' => 2, 'student_id' => 4, 'instructor_id' => 1],
            ['id' => 25, 'point' => 20, 'weight_id' => 2, 'student_id' => 5, 'instructor_id' => 1],
            ['id' => 26, 'point' => 15, 'weight_id' => 2, 'student_id' => 6, 'instructor_id' => 1],
            ['id' => 27, 'point' => 8,  'weight_id' => 2, 'student_id' => 7, 'instructor_id' => 1],
            ['id' => 28, 'point' => 19, 'weight_id' => 2, 'student_id' => 8, 'instructor_id' => 1],
            ['id' => 29, 'point' => 22, 'weight_id' => 2, 'student_id' => 9, 'instructor_id' => 1],
            ['id' => 30, 'point' => 15, 'weight_id' => 2, 'student_id' => 10, 'instructor_id' => 1],
            ['id' => 31, 'point' => 19, 'weight_id' => 2, 'student_id' => 11, 'instructor_id' => 1],
            ['id' => 32, 'point' => 22, 'weight_id' => 2, 'student_id' => 12, 'instructor_id' => 1],
            ['id' => 33, 'point' => 30, 'weight_id' => 2, 'student_id' => 13, 'instructor_id' => 1],
            ['id' => 34, 'point' => 28, 'weight_id' => 2, 'student_id' => 14, 'instructor_id' => 1],
            ['id' => 35, 'point' => 29, 'weight_id' => 2, 'student_id' => 15, 'instructor_id' => 1],
            ['id' => 36, 'point' => 21, 'weight_id' => 2, 'student_id' => 16, 'instructor_id' => 1],
            ['id' => 37, 'point' => 25, 'weight_id' => 2, 'student_id' => 17, 'instructor_id' => 1],
            ['id' => 38, 'point' => 12, 'weight_id' => 2, 'student_id' => 18, 'instructor_id' => 1],
            ['id' => 39, 'point' => 24, 'weight_id' => 2, 'student_id' => 19, 'instructor_id' => 1],
            ['id' => 40, 'point' => 22, 'weight_id' => 2, 'student_id' => 20, 'instructor_id' => 1],
            ['id' => 41, 'point' => 45, 'weight_id' => 3, 'student_id' => 1, 'instructor_id' => 1],
            ['id' => 42, 'point' => 19, 'weight_id' => 3, 'student_id' => 2, 'instructor_id' => 1],
            ['id' => 43, 'point' => 35, 'weight_id' => 3, 'student_id' => 3, 'instructor_id' => 1],
            ['id' => 44, 'point' => 22, 'weight_id' => 3, 'student_id' => 4, 'instructor_id' => 1],
            ['id' => 45, 'point' => 32, 'weight_id' => 3, 'student_id' => 5, 'instructor_id' => 1],
            ['id' => 46, 'point' => 20, 'weight_id' => 3, 'student_id' => 6, 'instructor_id' => 1],
            ['id' => 47, 'point' => 19, 'weight_id' => 3, 'student_id' => 7, 'instructor_id' => 1],
            ['id' => 48, 'point' => 36, 'weight_id' => 3, 'student_id' => 8, 'instructor_id' => 1],
            ['id' => 49, 'point' => 26, 'weight_id' => 3, 'student_id' => 9, 'instructor_id' => 1],
            ['id' => 50, 'point' => 38, 'weight_id' => 3, 'student_id' => 10, 'instructor_id' => 1],
            ['id' => 51, 'point' => 33, 'weight_id' => 3, 'student_id' => 11, 'instructor_id' => 1],
            ['id' => 52, 'point' => 35, 'weight_id' => 3, 'student_id' => 12, 'instructor_id' => 1],
            ['id' => 53, 'point' => 44, 'weight_id' => 3, 'student_id' => 13, 'instructor_id' => 1],
            ['id' => 54, 'point' => 34, 'weight_id' => 3, 'student_id' => 14, 'instructor_id' => 1],
            ['id' => 55, 'point' => 45, 'weight_id' => 3, 'student_id' => 15, 'instructor_id' => 1],
            ['id' => 56, 'point' => 39, 'weight_id' => 3, 'student_id' => 16, 'instructor_id' => 1],
            ['id' => 57, 'point' => 45, 'weight_id' => 3, 'student_id' => 17, 'instructor_id' => 1],
            ['id' => 58, 'point' => 25, 'weight_id' => 3, 'student_id' => 18, 'instructor_id' => 1],
            ['id' => 59, 'point' => 40, 'weight_id' => 3, 'student_id' => 19, 'instructor_id' => 1],
            ['id' => 60, 'point' => 33, 'weight_id' => 3, 'student_id' => 20, 'instructor_id' => 1],
        ]);
    }
}
