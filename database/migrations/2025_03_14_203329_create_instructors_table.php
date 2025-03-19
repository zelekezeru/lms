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
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('department_id');
            $table->string('specialization', 255);
            $table->enum('employment_type', ['full-time', 'part-time', 'adjunct']);
            $table->date('hire_date');
            $table->enum('status', ['active', 'inactive', 'suspended']);
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamps();

            // Foreign key constraints;
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
