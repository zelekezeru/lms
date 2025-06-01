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
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_session_id')->constrained();
                $table->foreignId('student_id')->constrained();
                $table->enum('status', ['permission', 'present', 'absent']);
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
