<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tracks')->insert([
            [
                'id' => 1,
                'name' => 'Biblical Study',
                'code' => 'DP/0001/25',
                'description' => 'BA in Theology in Biblical Studies Track',
                'program_id' => 1,
                'duration' => 4,
                'created_at' => '2025-04-11 07:22:46',
                'updated_at' => '2025-04-11 07:22:46',
            ],
            [
                'id' => 2,
                'name' => 'Pastoral Track',
                'code' => 'DP/0002/25',
                'description' => 'BA in Theology in Pastoral Track',
                'program_id' => 1,
                'duration' => 4,
                'created_at' => '2025-04-11 07:23:31',
                'updated_at' => '2025-04-11 07:23:31',
            ],
            [
                'id' => 3,
                'name' => 'Mission Track',
                'code' => 'DP/0003/25',
                'description' => 'BA in Theology in Mission Track',
                'program_id' => 1,
                'duration' => 4,
                'created_at' => '2025-04-11 07:23:57',
                'updated_at' => '2025-04-11 07:23:57',
            ],
            [
                'id' => 4,
                'name' => 'Leadership Track',
                'code' => 'DP/0004/25',
                'description' => 'BA in Theology in Leadership Track',
                'program_id' => 1,
                'duration' => 4,
                'created_at' => '2025-04-11 07:24:26',
                'updated_at' => '2025-04-11 07:24:26',
            ],
            [
                'id' => 5,
                'name' => 'Counseling Track',
                'code' => 'DP/0005/25',
                'description' => 'BA in Theology in Counseling Track',
                'program_id' => 1,
                'duration' => 4,
                'created_at' => '2025-04-11 07:24:55',
                'updated_at' => '2025-04-11 07:24:55',
            ],
            [
                'id' => 6,
                'name' => 'Biblical Study Track',
                'code' => 'DP/0006/25',
                'description' => 'MDIV in Biblical Study Track',
                'program_id' => 5,
                'duration' => 4,
                'created_at' => '2025-04-11 07:25:45',
                'updated_at' => '2025-04-11 07:25:45',
            ],
            [
                'id' => 7,
                'name' => 'Christian Leadership Track',
                'code' => 'DP/0007/25',
                'description' => 'MDIV in Christian Leadership Track',
                'program_id' => 5,
                'duration' => 4,
                'created_at' => '2025-04-11 07:26:39',
                'updated_at' => '2025-04-11 07:26:39',
            ],
            [
                'id' => 8,
                'name' => 'Advanced Diploma in Theology English Track',
                'code' => 'DP/008/25',
                'description' => 'Advanced Diploma in Theology Track in English',
                'program_id' => 2,
                'duration' => 4,
                'created_at' => '2025-04-11 09:09:32',
                'updated_at' => '2025-04-11 09:15:27',
            ],
            [
                'id' => 9,
                'name' => 'MABTS Track',
                'code' => 'DP/009/25',
                'description' => 'MABTS Track in English',
                'program_id' => 3,
                'duration' => 4,
                'created_at' => '2025-04-11 09:11:19',
                'updated_at' => '2025-04-11 09:11:19',
            ],
            [
                'id' => 10,
                'name' => 'MTH Track',
                'code' => 'DP/010/25',
                'description' => 'MTH Track in English',
                'program_id' => 4,
                'duration' => 4,
                'created_at' => '2025-04-11 09:11:54',
                'updated_at' => '2025-04-11 09:11:54',
            ],
            [
                'id' => 11,
                'name' => 'Post Graduate Diploma English Track',
                'code' => 'DP/011/25',
                'description' => 'Post Graduate Diploma Track in English',
                'program_id' => 6,
                'duration' => 4,
                'created_at' => '2025-04-11 09:12:44',
                'updated_at' => '2025-04-11 09:14:24',
            ],
            [
                'id' => 12,
                'name' => 'Advance Diploma in Theology Amharic Track',
                'code' => 'DP/012/25',
                'description' => 'Advance Diploma in Theology Amharic Track',
                'program_id' => 7,
                'duration' => 4,
                'created_at' => '2025-04-11 09:13:32',
                'updated_at' => '2025-04-11 09:13:32',
            ],
            [
                'id' => 13,
                'name' => 'Bachelors of Art in Theology Amharic Track',
                'code' => 'DP/013/25',
                'description' => 'Bachelors of Art in Theology Amharic Track',
                'program_id' => 8,
                'duration' => 4,
                'created_at' => '2025-04-11 09:17:08',
                'updated_at' => '2025-04-11 09:17:08',
            ],
            [
                'id' => 14,
                'name' => 'Distance Learning Track',
                'code' => 'DP/014/25',
                'description' => 'Distance Learning Track in Amharic',
                'program_id' => 9,
                'duration' => 4,
                'created_at' => '2025-04-11 09:17:08',
                'updated_at' => '2025-04-11 09:17:08',
            ],
        ]);

        $data = [];
        $isCommon = 0;

        for ($trackId = 1; $trackId <= 14; $trackId++) {
            for ($courseId = 1; $courseId <= 45; $courseId++) {
                $data[] = [
                    'course_id' => $courseId,
                    'track_id' => $trackId,
                    'is_common' => $isCommon,
                ];
            }
        }

        DB::table('course_track')->insert($data);
    }
}
