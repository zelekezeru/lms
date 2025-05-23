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

        $data = [];
        $yearId = 1;
        $userId = 2;
        $semesterId = 1;
        $createdAt = '2025-04-15 14:56:25';
        $updatedAt = '2025-04-15 14:56:25';

        for ($i = 1; $i <= 13; $i++) {
            $programId = $i;
            $trackId = match ($i) {
                1 => 1,
                2 => 6,
                3 => 8,
                4 => 9,
                5 => 10,
                6 => 11,
                7 => 12,
                8 => 13,
                default => null, // Handle cases beyond 8 if needed
            };

            if ($trackId !== null) {
                $data[] = [
                    'id' => $i,
                    'name' => 'Section 1',
                    'code' => 'SC-25-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'year_id' => $yearId,
                    'user_id' => $userId,
                    'program_id' => $programId,
                    'track_id' => $trackId,
                    'semester_id' => $semesterId,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];
            }
        }

        DB::table('sections')->insert($data);   

        $data = [];
        $isCommon = 0;

        // Ensure course_section references valid section IDs
        $validSectionIds = DB::table('sections')->pluck('id')->toArray();
        $instructor = 1;

        foreach ($validSectionIds as $sectionId) {
            for ($courseId = 1; $courseId <= 30; $courseId++) {
                $data[] = [
                    'course_id' => $courseId,
                    'section_id' => $sectionId,
                ];
            }
        }

        DB::table('course_section')->insert($data);
    }
}
