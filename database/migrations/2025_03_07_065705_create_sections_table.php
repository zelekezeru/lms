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
            $table->foreignId('year_id')->nullable()->constrained();
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
