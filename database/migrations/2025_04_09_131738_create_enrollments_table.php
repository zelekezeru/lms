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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_offering_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');

            // Financial status is tracked in invoices table — no need to duplicate it here
            $table->enum('status', ['pending', 'enrolled', 'dropped', 'deferred'])->default('pending');

            // Tracks student academic progress for this course
            $table->enum('academic_status', ['in_progress', 'completed', 'failed', 'dropped'])->default('in_progress');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollemnts');
    }
};
