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
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->enum('grade_submission_status', ['pending', 'approved', 'rejected'])->nullable()->after('completed');
            $table->timestamp('grade_submission_requested_at')->nullable()->after('grade_submission_status');
            $table->timestamp('grade_submission_approved_at')->nullable()->after('grade_submission_requested_at');
            $table->foreignId('grade_submission_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('grade_submission_approved_at');
            $table->text('grade_submission_notes')->nullable()->after('grade_submission_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropForeign(['grade_submission_approved_by']);
            $table->dropColumn([
                'grade_submission_status',
                'grade_submission_requested_at',
                'grade_submission_approved_at',
                'grade_submission_approved_by',
                'grade_submission_notes',
            ]);
        });
    }
};
