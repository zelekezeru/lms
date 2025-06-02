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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('instructor_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->foreignId('room_id')->nullable()->constrained();

            $table->string('class_about')->nullable(); // What the class was about
            $table->enum('type', ['in-person', 'online']);
            $table->boolean('isCompleted')->default(false);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
