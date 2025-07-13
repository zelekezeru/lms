<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the course IDs for each year and semester
        $courseIdsByYearSemester = [
            [
                1 => [ // Year Level 1
                    1 => [1, 6, 3, 2, 4],       // Semester 1
                    2 => [5, 7, 10, 9, 11],     // Semester 2
                ],
                2 => [ // Year Level 2
                    1 => [12, 8, 13, 14, 15],
                    2 => [16, 17, 18, 19, 20],
                ],
                3 => [ // Year Level 3
                    1 => [21, 22, 23, 24, 25],
                    2 => [26, 27, 30],          // removed 29 (not in list)
                ],
                4 => [ // Year Level 4
                    1 => [31, 34, 32, 33, 35],
                    2 => [36, 37, 40, 38, 39],
                ],
            ]

        ];

        // Prepare an empty array to hold all records before inserting
        $recordsToInsert = [];

        $tracks = 2; // Assuming track_id is 2, adjust as needed

        // Define common data that applies to all records
        $commonData = [
            'study_mode_id' => 1,
            'description'   => 'This Course In This Study MOde And Track Should Be Taken',
            'active'        => 0,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];

        for ($i = 1; $i <= $tracks; $i++) {

            if ($i == 1) {
                $commonData['track_id'] = 1; // Biblical Study Track
                $commonData['study_mode_id'] = 1;
            } else {
                $commonData['track_id'] = 14; // Adjust for other tracks
                $commonData['study_mode_id'] = 4;
            }

            // Loop through each year level (1 to 4)
            for ($yearLevel = 1; $yearLevel <= 4; $yearLevel++) {
                // Loop through each semester (1 to 2) for the current year level
                for ($semester = 1; $semester <= 2; $semester++) {
                    // Get the specific list of course IDs for the current year and semester
                    $currentCourseIds = $courseIdsByYearSemester[$yearLevel][$semester];

                    // Loop through each course ID in the current list
                    foreach ($currentCourseIds as $courseId) {
                        // Create a new record by merging common data with specific data
                        $record = array_merge($commonData, [
                            'course_id'  => $courseId,
                            'year_level' => $yearLevel,
                            'semester_level'   => $semester,
                            'study_mode_id' => $commonData['study_mode_id'],

                        ]);
                        // Add the created record to our collection
                        $recordsToInsert[] = $record;
                    }
                }
            }
        }

        // Insert all collected records into the database at once
        DB::table('curricula')->insert($recordsToInsert);
    }
}
