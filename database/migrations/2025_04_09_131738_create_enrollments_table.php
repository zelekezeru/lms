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
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');

            // Tracks the student's **financial/enrollment status** for this course:
            // - 'pending'   → The student has not completed payment yet.
            // - 'enrolled'  → Payment is completed; the student is officially enrolled.
            // - 'dropped'   → Student dropped the course before it started (after payment).
            $table->enum('status', ['pending', 'enrolled', 'dropped'])->default('pending');

            // Tracks the student's **academic progress** in the course:
            // - 'in_progress' → The student is currently taking the course.
            // - 'completed'   → The student finished and passed the course.
            // - 'failed'      → The student finished but did not pass (e.g., grade F).
            $table->enum('academic_status', ['in_progress', 'completed', 'failed'])->nullable()->default('in_progress');

            // If Student Drops A course
            $table->timestamp('dropped_at')->nullable();
            $table->string('drop_reason')->nullable();

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
