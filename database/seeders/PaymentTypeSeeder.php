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
                'duration' => 'One-Time',
                'study_mode_id' => 1,
                'payment_category_id' => 1,
                'is_active' => true,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
            [
                'type' => 'Course Fee',
                'language' => 'Hermeneutics',
                'amount' => 450.00,
                'duration' => 'Per-Course',
                'study_mode_id' => 2,
                'payment_category_id' => 2,
                'is_active' => true,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
            [
                'type' => 'Semester',
                'language' => null,
                'amount' => 400.00,
                'duration' => 'Per-Semester',
                'study_mode_id' => 3,
                'payment_category_id' => 3,
                'is_active' => true,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
        ]);
    }
}
