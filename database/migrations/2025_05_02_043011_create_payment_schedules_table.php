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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_amount');
            $table->integer('installment_amount');
            $table->integer('installment_count');
            $table->integer('installment_interval')->comment('in days');
            $table->integer('grace_period')->comment('in days');
            $table->integer('late_fee')->nullable()->comment('in percentage');
            $table->integer('late_fee_amount')->nullable()->comment('in percentage');
            $table->integer('discount')->nullable()->comment('in percentage');
            $table->integer('discount_amount')->nullable()->comment('in percentage');
            $table->integer('penalty')->nullable()->comment('in percentage');
            $table->integer('penalty_amount')->nullable()->comment('in percentage');
            $table->integer('payment_method')->comment('1: cash, 2: bank transfer, 3: cheque, 4: credit card');
            $table->integer('payment_status')->comment('1: pending, 2: completed, 3: failed, 4: cancelled');
            $table->boolean('is_active')->default(true);
            $table->foreignId('payment_category_id')->constrained('payment_categories')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
