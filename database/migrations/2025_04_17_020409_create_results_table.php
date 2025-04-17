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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->integer('result_point');
            $table->string('result_description')->nullable();
            $table->string('changed_point')->nullable();

            $table->foreignId('weight_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('year_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('grade_id')->constrained();

            $table->foreignId('changed_by')->nullable();
            $table->date('changed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
