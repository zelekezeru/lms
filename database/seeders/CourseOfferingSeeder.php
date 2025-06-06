<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseOfferingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('course_offerings')->insert([
            [
            'course_id' => 1, 'section_id' => 1, 'instructor_id' => 1,
            'completed' => 0, 'year_level' => 1, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 2, 'section_id' => 1, 'instructor_id' => 2,
            'completed' => 0, 'year_level' => 1, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 3, 'section_id' => 1, 'instructor_id' => 3,
            'completed' => 0, 'year_level' => 1, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 4, 'section_id' => 1, 'instructor_id' => 4,
            'completed' => 0, 'year_level' => 1, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 5, 'section_id' => 1, 'instructor_id' => 1,
            'completed' => 0, 'year_level' => 1, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 6, 'section_id' => 1, 'instructor_id' => 2,
            'completed' => 0, 'year_level' => 1, 'semester' => 2,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 7, 'section_id' => 1, 'instructor_id' => 3,
            'completed' => 0, 'year_level' => 1, 'semester' => 2,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 8, 'section_id' => 1, 'instructor_id' => 4,
            'completed' => 0, 'year_level' => 1, 'semester' => 2,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 9, 'section_id' => 1, 'instructor_id' => 1,
            'completed' => 0, 'year_level' => 1, 'semester' => 2,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 10, 'section_id' => 1, 'instructor_id' => 2,
            'completed' => 0, 'year_level' => 1, 'semester' => 2,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 11, 'section_id' => 1, 'instructor_id' => 3,
            'completed' => 0, 'year_level' => 2, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 12, 'section_id' => 1, 'instructor_id' => 4,
            'completed' => 0, 'year_level' => 2, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 13, 'section_id' => 1, 'instructor_id' => null,
            'completed' => 0, 'year_level' => 2, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 14, 'section_id' => 1, 'instructor_id' => null,
            'completed' => 0, 'year_level' => 2, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 15, 'section_id' => 1, 'instructor_id' => null,
            'completed' => 0, 'year_level' => 2, 'semester' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
            'course_id' => 16, 'section_id' => 1, 'instructor_id' => null,
            'completed' => 0, 'year_level' => 2, 'semester' => 2,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 17, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 2, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 18, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 2, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 19, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 2, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 20, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 2, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 21, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 22, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 23, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 24, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 25, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 26, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 27, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 28, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 29, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 30, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 3, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 31, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 32, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 33, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 34, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 35, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 36, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 37, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 38, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 39, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 40, 'section_id' => 1, 'instructor_id' => null,
                'completed' => 0, 'year_level' => 4, 'semester' => 2,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
