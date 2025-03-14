<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('code');
            $table->string('phone', 15)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address');
            $table->string('contact_person');
            $table->string('contact_phone', 15)->nullable();
            $table->longText('aggrement');
            $table->foreignId('user_id')->constrained()->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('allowed')->default(false);
            $table->boolean('paid')->default(false);
            
            $table->string('password');
            $table->boolean('password_changed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
