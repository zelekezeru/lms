<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->insert([
            [
                'payment_type' => 'Bank Transfer',
                'bank_name' => 'CBE',
                'account_number' => '1234567890',
                'is_active' => true,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
            [
                'payment_type' => 'Cash',
                'bank_name' => null,
                'account_number' => null,
                'is_active' => true,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
            [
                'payment_type' => 'Mobile Payment',
                'bank_name' => 'Telebirr',
                'account_number' => '09876543210',
                'is_active' => true,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
            [
                'payment_type' => 'Cheque',
                'bank_name' => 'Birhan Bank',
                'account_number' => '1122334455',
                'is_active' => true,
                'tenant_id' => 1,
                'created_by' => 1,
            ],
        ]);
    }
}
