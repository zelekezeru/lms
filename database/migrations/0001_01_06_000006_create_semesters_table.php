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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('year_id')->constrained();
            $table->integer('level'); // the level of semesters indicates the order of the semester in the year so if it is the first semester it's level is 1, if semester 2  level is just 2....
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive'); // Only one can be active
            $table->boolean('is_approved')->default(1);
            $table->boolean('is_completed')->default(0);
            $table->timestamps();

            $table->unique(['year_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
