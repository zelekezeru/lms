<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programs')->insert([
            [
                'id' => 1,
                'name' => 'Bachelors of Art in Theology',
                'language' => 'English',
                'code' => 'PR/01/25',
                'status' => 'Active',
                'duration' => 4,
                'description' => 'Bachelors of Art in Theology in English',
                'user_id' => 2,
                'created_at' => '2025-04-11 05:39:14',
                'updated_at' => '2025-04-11 05:39:14',
            ],
            [
                'id' => 2,
                'name' => 'Advance Diploma in Theology',
                'language' => 'English',
                'code' => 'PR/02/25',
                'status' => 'Active',
                'duration' => 3,
                'description' => 'Advance Diploma in Theology in English',
                'user_id' => 2,
                'created_at' => '2025-04-11 07:13:34',
                'updated_at' => '2025-04-11 07:13:34',
            ],
            [
                'id' => 3,
                'name' => 'MABTS (Masters of Art in Biblical Study)',
                'language' => 'English',
                'code' => 'PR/03/25',
                'status' => 'Active',
                'duration' => 4,
                'description' => 'Masters of Art in Biblical Study in English',
                'user_id' => 2,
                'created_at' => '2025-04-11 07:14:39',
                'updated_at' => '2025-04-11 07:14:39',
            ],
            [
                'id' => 4,
                'name' => 'MTH (Masters of Arts in Theology)',
                'language' => 'English',
                'code' => 'PR/04/25',
                'status' => 'Active',
                'duration' => 4,
                'description' => 'Masters of Arts in Theology in English',
                'user_id' => 2,
                'created_at' => '2025-04-11 07:15:33',
                'updated_at' => '2025-04-11 07:15:33',
            ],
            [
                'id' => 5,
                'name' => 'MDIV (Masters of Divinity)',
                'language' => 'English',
                'code' => 'PR/05/25',
                'status' => 'Active',
                'duration' => 4,
                'description' => 'Masters of Divinity in English',
                'user_id' => 2,
                'created_at' => '2025-04-11 07:16:45',
                'updated_at' => '2025-04-11 07:16:45',
            ],
            [
                'id' => 6,
                'name' => 'Post Graduate Diploma',
                'language' => 'English',
                'code' => 'PR/06/25',
                'status' => 'Active',
                'duration' => 3,
                'description' => 'Post Graduate Diploma in English',
                'user_id' => 2,
                'created_at' => '2025-04-11 07:17:35',
                'updated_at' => '2025-04-11 07:17:35',
            ],
            [
                'id' => 7,
                'name' => 'Advance Diploma in Theology',
                'language' => 'Amharic',
                'code' => 'PR/07/25',
                'status' => 'Active',
                'duration' => 4,
                'description' => 'Advance Diploma in Theology in Amharic',
                'user_id' => 2,
                'created_at' => '2025-04-11 07:20:07',
                'updated_at' => '2025-04-11 07:20:07',
            ],
            [
                'id' => 8,
                'name' => 'Bachelors of Art in Theology',
                'language' => 'Amharic',
                'code' => 'PR/08/25',
                'status' => 'Active',
                'duration' => 4,
                'description' => 'Bachelors of Art in Theology in Amharic',
                'user_id' => 2,
                'created_at' => '2025-04-11 07:21:25',
                'updated_at' => '2025-04-11 07:21:25',
            ],
        ]);

        // Insert data into program_study_mode table

        $data = [];

        // Loop for program_id values 1 to 8
        for ($programId = 1; $programId <= 8; $programId++) {
            // Define study mode IDs.
            $studyModeIds = [1, 2, 3, 4];
            $duration = ($programId >= 1 && $programId <= 4) || ($programId >= 7 && $programId <= 8) ? 4 : 3;
            
            // Loop through study mode ids
            foreach ($studyModeIds as $studyModeId) {
                $data[] = [
                    'program_id' => $programId,
                    'study_mode_id' => $studyModeId,
                    'duration' => $duration,
                    'created_at' => now(),
                ];
            }
        }
        DB::table('program_study_mode')->insert($data);

    }
}
