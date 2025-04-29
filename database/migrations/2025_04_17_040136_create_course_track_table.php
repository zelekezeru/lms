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
        Schema::create('course_track', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('track_id')->constrained()->onDelete('CASCADE');
            $table->boolean('is_common')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_track');
    }
};
