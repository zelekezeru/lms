<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(TenantSeeder::class);
    }
}
