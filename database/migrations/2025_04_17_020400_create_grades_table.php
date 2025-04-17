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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('grade_point');
            $table->string('grade_letter');
            $table->string('grade_description')->nullable();
            $table->string('grade_scale');
            
            $table->foreignId('student_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('year_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
