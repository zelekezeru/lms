<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_types')->insert([
            [
                'type' => 'Registration Fee',
                'language' => 'English',
                'amount' => 1200.00,
                'duration' => 'one-time',
                'is_active' => true,
                'study_mode_id' => 1,
                'payment_category_id' => 1,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
            [
                'type' => 'Course Fee',
                'language' => 'English',
                'amount' => 450.00,
                'duration' => 'per-course',
                'is_active' => true,
                'study_mode_id' => 1,
                'tenant_id' => 1,
                'created_by' => 1,
                'payment_category_id' => 2,
            ],
            [
                'type' => 'Semester Registration',
                'language' => 'English',
                'amount' => 400.00,
                'duration' => 'per-semester',
                'study_mode_id' => 1,
                'payment_category_id' => 3,
                'is_active' => true,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
        ]);
    }
}
