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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();

            $table->boolean('is_active')->nullable()->default(1);            
            $table->boolean('is_deleted')->nullable()->default(0);
            $table->boolean('is_approved')->nullable()->default(0);
            $table->boolean('is_completed')->nullable()->default(0);
            $table->boolean('is_verified')->nullable()->default(0);
            $table->boolean('is_enrolled')->nullable()->default(0);
            $table->boolean('is_graduated')->nullable()->default(0);
            $table->boolean('is_scholarship')->nullable()->default(0);
            $table->boolean('is_scholarship_approved')->nullable()->default(0);
            $table->boolean('is_scholarship_verified')->nullable()->default(0);

            $table->string('created_by_name')->nullable();
            $table->string('updated_by_name')->nullable();
            $table->string('deleted_by_name')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->string('completed_by_name')->nullable();
            $table->string('verified_by_name')->nullable();
            $table->string('enrolled_by_name')->nullable();
            $table->string('graduated_by_name')->nullable();
            $table->string('scholarship_by_name')->nullable();
            $table->string('scholarship_approved_by_name')->nullable();
            $table->string('scholarship_verified_by_name')->nullable();
            
            
            $table->date('deleted_at')->nullable();
            $table->date('approved_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->date('verified_at')->nullable();
            $table->date('enrolled_at')->nullable();
            $table->date('graduated_at')->nullable();
            $table->date('scholarship_at')->nullable();
            $table->date('scholarship_approved_at')->nullable();
            $table->date('scholarship_verified_at')->nullable();
            
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
        Schema::dropIfExists('statuses');
    }
};
