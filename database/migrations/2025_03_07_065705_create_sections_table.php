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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('program_id')->constrained();
            $table->foreignId('track_id')->constrained();
            $table->foreignId('study_mode_id')->nullable()->constrained();

            // these stands for batch so if a section is at year level 1 at 2025 then the batch(year_id) will be a link to 2025 on years table
            $table->foreignId('year_id')->nullable()->constrained();
            // these stands for the current status of the section so if the section is 2 years in to the program they are called 2nd years(just 2)
            $table->foreignId('semester_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
