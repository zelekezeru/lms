<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('id_no', 20)->unique();
            $table->string('old_id', 20)->nullable();
            $table->string('first_name', 100);
            $table->string('middle_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('mobile_phone', 15);
            $table->string('office_phone', 15)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('marital_status', 10)->nullable();
            $table->char('sex', 6);
            $table->string('address', 200)->nullable();

            $table->string('student_signature', 100)->nullable();
            $table->text('office_use_notes')->nullable();
            $table->string('payment_code', 100)->nullable();

            $table->foreignId('tenant_id')->nullable()->constrained();
            $table->foreignId('program_id')->constrained();
            $table->foreignId('track_id')->constrained();
            $table->foreignId('study_mode_id')->nullable()->constrained();
            $table->foreignId('section_id')->nullable()->constrained();
            $table->foreignId('year_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
