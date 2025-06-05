<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms')->insert([
            [
                'name' => 'Class Room 1',
                'capacity' => 50,
                'location' => 'Main Building Ground',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Class Room 2',
                'capacity' => 50,
                'location' => 'Main Building Ground',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Class Room 3',
                'capacity' => 50,
                'location' => 'Main Building Ground',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Class Room 4',
                'capacity' => 50,
                'location' => 'Main Building 1st Floor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Class Room 5',
                'capacity' => 50,
                'location' => 'Main Building 1st Floor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Class Room 6',
                'capacity' => 50,
                'location' => 'Main Building 1st Floor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Class Room 7',
                'capacity' => 50,
                'location' => 'Main Building 1st Floor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Smart Room',
                'capacity' => 50,
                'location' => 'Main Building 1st Floor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Computer Lab',
                'capacity' => 40,
                'location' => 'Main Building 1st Floor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Library Reading Room',
                'capacity' => 50,
                'location' => 'Library Building',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Main Hall',
                'capacity' => 300,
                'location' => 'Main Meeting Hall',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}