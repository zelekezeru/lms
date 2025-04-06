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
            $table->string('email')->unique();
            $table->string('code')->nullable();
            $table->string('logo')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('address');
            $table->string('contact_person');
            $table->string('contact_phone', 15)->nullable();
            $table->string('contact_email')->nullable();
            $table->longText('aggrement')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('allowed')->default(false);
            $table->boolean('paid')->default(false);
            $table->dateTime('deleted_at')->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('password');
            $table->boolean('password_changed')->default(false);
            $table->string('default_password')->nullable();
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
