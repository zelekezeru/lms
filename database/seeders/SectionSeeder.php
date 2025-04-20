<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sections')->insert([
            [
                'id' => 1,
                'name' => 'Section 1',
                'code' => 'SC-25-01',
                'year_id' => 1,
                'user_id' => 2,
                'program_id' => 1,
                'department_id' => 1,
                'semester_id' => 1,
                'created_at' => '2025-04-15 14:56:25',
                'updated_at' => '2025-04-15 14:56:25',
            ],
            [
                'id' => 2,
                'name' => 'Section 1',
                'code' => 'SC-25-02',
                'year_id' => 1,
                'user_id' => 2,
                'program_id' => 2,
                'department_id' => 6,
                'semester_id' => 1,
                'created_at' => '2025-04-15 14:56:25',
                'updated_at' => '2025-04-15 14:56:25',
            ],
            [
                'id' => 3,
                'name' => 'Section 1',
                'code' => 'SC-25-03',
                'year_id' => 1,
                'user_id' => 2,
                'program_id' => 3,
                'department_id' => 8,
                'semester_id' => 1,
                'created_at' => '2025-04-15 14:56:25',
                'updated_at' => '2025-04-15 14:56:25',
            ],
            [
                'id' => 4,
                'name' => 'Section 1',
                'code' => 'SC-25-04',
                'year_id' => 1,
                'user_id' => 2,
                'program_id' => 4,
                'department_id' => 9,
                'semester_id' => 1,
                'created_at' => '2025-04-15 14:56:25',
                'updated_at' => '2025-04-15 14:56:25',
            ],
            [
                'id' => 5,
                'name' => 'Section 1',
                'code' => 'SC-25-05',
                'year_id' => 1,
                'user_id' => 2,
                'program_id' => 5,
                'department_id' => 10,
                'semester_id' => 1,
                'created_at' => '2025-04-15 14:56:25',
                'updated_at' => '2025-04-15 14:56:25',
            ],
            [
                'id' => 6,
                'name' => 'Section 1',
                'code' => 'SC-25-06',
                'year_id' => 1,
                'user_id' => 2,
                'program_id' => 6,
                'department_id' => 11,
                'semester_id' => 1,
                'created_at' => '2025-04-15 14:56:25',
                'updated_at' => '2025-04-15 14:56:25',
            ],
            [
                'id' => 7,
                'name' => 'Section 1',
                'code' => 'SC-25-07',
                'year_id' => 1,
                'user_id' => 2,
                'program_id' => 7,
                'department_id' => 12,
                'semester_id' => 1,
                'created_at' => '2025-04-15 14:56:25',
                'updated_at' => '2025-04-15 14:56:25',
            ],
            [
                'id' => 8,
                'name' => 'Section 1',
                'code' => 'SC-25-08',
                'year_id' => 1,
                'user_id' => 2,
                'program_id' => 8,
                'department_id' => 13,
                'semester_id' => 1,
                'created_at' => '2025-04-15 14:56:25',
                'updated_at' => '2025-04-15 14:56:25',
            ],
        ]);
    }
}
