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
            $table->string('academic_year', 10);
            $table->string('semester', 20);
            $table->string('student_id', 20);
            $table->string('program', 50);
            $table->string('year_of_study', 10);
            $table->string('student_name', 100);
            $table->string('father_name', 100);
            $table->string('grand_father_name', 100);
            $table->string('home_phone', 15)->nullable();
            $table->string('mobile_phone', 15)->nullable();
            $table->string('office_phone', 15)->nullable();
            $table->date('date_of_birth');
            $table->string('marital_status', 10);
            $table->char('sex', 1);
            $table->string('pastor_name', 100)->nullable();
            $table->string('address_1', 200)->nullable();
            $table->string('address_2', 200)->nullable();
            $table->string('position_denomination', 100)->nullable();
            $table->integer('total_credit_hours');
            $table->decimal('total_amount_paid', 10, 2);
            $table->string('student_signature', 100)->nullable();
            $table->text('office_use_notes')->nullable();
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
