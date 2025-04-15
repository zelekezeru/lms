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
            $table->string('mobile_phone', 15);
            $table->string('office_phone', 15)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('marital_status', 10)->nullable();
            $table->char('sex', 6);
            
            $table->string('address', 200)->nullable();
            $table->string('pastor_name', 100)->nullable();
            $table->string('pastor_phone', 100)->nullable();
            $table->string('church_name', 100)->nullable();
            $table->string('church_address', 200)->nullable();
            $table->string('position_denomination', 100)->nullable();
            
            $table->string('student_signature', 100)->nullable();
            $table->text('office_use_notes')->nullable();
            $table->string('profile_image')->nullable();
            
            $table->foreignId('tenant_id')->nullable()->constrained();
            $table->foreignId('program_id')->constrained();
            $table->foreignId('department_id')->constrained();
            $table->foreignId('year_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->foreignId('section_id')->nullable()->constrained();
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('is_active')->nullable()->default(1)->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->foreignId('deleted_at')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('approved_at')->nullable()->constrained('users');
            $table->foreignId('completed_by')->nullable()->constrained('users');
            $table->foreignId('completed_at')->nullable()->constrained('users');
            $table->foreignId('is_approved')->nullable()->constrained('users');
            $table->foreignId('is_completed')->nullable()->constrained('users');
            
            $table->foreignId('is_deleted')->nullable()->constrained('users');
            $table->foreignId('is_verified')->nullable()->constrained('users');
            $table->foreignId('is_enrolled')->nullable()->constrained('users');
            $table->foreignId('is_graduated')->nullable()->constrained('users');
            $table->foreignId('is_scholarship')->nullable()->constrained('users');
            $table->foreignId('is_scholarship_approved')->nullable()->constrained('users');
            $table->foreignId('is_scholarship_verified')->nullable()->constrained('users');
            $table->foreignId('is_scholarship_verified_by')->nullable()->constrained('users');
            $table->foreignId('is_scholarship_verified_at')->nullable()->constrained('users');
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
