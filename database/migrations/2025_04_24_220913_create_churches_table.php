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
        Schema::create('churches', function (Blueprint $table) {
            $table->id();
            $table->string('pastor_name', 100)->nullable();
            $table->string('pastor_phone', 100)->nullable();
            $table->string('church_name', 100)->nullable();
            $table->string('church_address', 200)->nullable();
            $table->string('position_denomination', 100)->nullable();

            $table->foreignId('student_id')->nullable()->constrained('students');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('churches');
    }
};
