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
            $table->string('student_name', 100);
            $table->string('father_name', 100);
            $table->string('grand_father_name', 100)->nullable();
            $table->string('mobile_phone', 15)->nullable();
            $table->string('office_phone', 15)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('marital_status', 10)->nullable();
            $table->char('sex', 6);
            $table->string('semester', 20)->nullable();
            $table->string('program', 50)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('pastor_name', 100)->nullable();
            $table->string('pastor_phone', 100)->nullable();
            $table->string('church_name', 100)->nullable();
            $table->string('church_address', 200)->nullable();
            $table->string('position_denomination', 100)->nullable();
            $table->integer('total_credit_hours')->nullable();
            $table->decimal('total_amount_paid', 10, 2)->nullable();
            $table->decimal('total_amount_due', 10, 2)->nullable();
            $table->string('student_signature', 100)->nullable();
            $table->text('office_use_notes')->nullable();
            $table->string('profile_image')->nullable();
            
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained();
            $table->foreignId('year_id')->nullable()->constrained();
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
