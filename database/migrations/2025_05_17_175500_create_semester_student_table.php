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
        Schema::create('semester_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');

            // Whether or not the student payed for the semester
            $table->enum('payment_status', ['paid', 'unpaid']);
            // Whether the student is currently learning in this semester, completed this semester or dropped for some reason
            $table->enum('academic_status', ['in_progress', 'completed', 'dropped']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester_student');
    }
};
