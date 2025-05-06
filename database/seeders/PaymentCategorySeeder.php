<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_categories')->insert([
            ['name' => 'Registration', 'description' => 'Payment for Registration', 'tenant_id' => 1, 'created_by' => 1],
            ['name' => 'Tuition', 'description' => 'Payment for Tuition', 'tenant_id' => 1, 'created_by' => 1],
            ['name' => 'Examination', 'description' => 'Payment for Examination', 'tenant_id' => 1, 'created_by' => 1],
            ['name' => 'Book', 'description' => 'Payment for Book', 'tenant_id' => 1, 'created_by' => 1],
            ['name' => 'Graduation', 'description' => 'Payment for Graduation', 'tenant_id' => 1, 'created_by' => 1],
            ['name' => 'Library', 'description' => 'Payment for Library', 'tenant_id' => 1, 'created_by' => 1],
            ['name' => 'Miscellaneous', 'description' => 'Miscellaneous Payments', 'tenant_id' => 1, 'created_by' => 1],
            ['name' => 'Other', 'description' => 'Other Payments', 'tenant_id' => 1, 'created_by' => 1],
        ]);
    }
}
