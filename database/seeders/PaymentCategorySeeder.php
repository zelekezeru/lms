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
            ['name' => 'Registration', 'description' => 'Payment for Registration'],
            ['name' => 'Tuition', 'description' => 'Payment for Tuition'],
            ['name' => 'Examination', 'description' => 'Payment for Examination'],
            ['name' => 'Book', 'description' => 'Payment for Book'],
            ['name' => 'Graduation', 'description' => 'Payment for Graduation'],
            ['name' => 'Library', 'description' => 'Payment for Library'],
            ['name' => 'Miscellaneous', 'description' => 'Miscellaneous Payments'],
            ['name' => 'Other', 'description' => 'Other Payments'],
        ]);
    }
}
