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
                'name' => '1st Semester of 2025',
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
                'name' => '1st Semester of 2024',
                'year_id' => 2,
                'level' => 1,
                'start_date' => '2024-01-10',
                'end_date' => '2024-04-10',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],
            [
                'name' => '2nd - 2024',
                'year_id' => 2,
                'level' => 2,
                'start_date' => '2024-04-15',
                'end_date' => '2024-07-15',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],  
            [
                'name' => '1st Semester of 2023',
                'year_id' => 3,
                'level' => 1,
                'start_date' => '2023-01-10',
                'end_date' => '2023-04-10',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],
            [
                'name' => '2nd - 2023',
                'year_id' => 3,
                'level' => 2,
                'start_date' => '2023-04-15',
                'end_date' => '2023-07-15',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],    
            [
                'name' => '1st Semester of 2022',
                'year_id' => 4,
                'level' => 1,
                'start_date' => '2022-01-10',
                'end_date' => '2022-04-10',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],
            [
                'name' => '2nd - 2022',
                'year_id' => 4,
                'level' => 2,
                'start_date' => '2022-04-15',
                'end_date' => '2022-07-15',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],   
            [
                'name' => '1st Semester of 2021',
                'year_id' => 5,
                'level' => 1,
                'start_date' => '2021-01-10',
                'end_date' => '2021-04-10',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],
            [
                'name' => '2nd - 2021',
                'year_id' => 5,
                'level' => 2,
                'start_date' => '2021-04-15',
                'end_date' => '2021-07-15',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],
            [
                'name' => '1st Semester of 2020',
                'year_id' => 6,
                'level' => 1,
                'start_date' => '2020-01-10',
                'end_date' => '2020-04-10',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ],
            [
                'name' => '2nd - 2020',
                'year_id' => 6,
                'level' => 2,
                'start_date' => '2020-04-15',
                'end_date' => '2020-07-15',
                'status' => 'Inactive',
                'is_approved' => 1,
                'is_completed' => 1,
            ], 

        ]);
    }
}
