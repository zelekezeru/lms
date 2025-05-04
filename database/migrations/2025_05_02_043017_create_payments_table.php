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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->integer('amount')->comment('in percentage');
            $table->integer('total_amount');
            $table->integer('payment_category_id')->constrained('payment_categorys')->cascadeOnDelete();
            $table->integer('payment_type_id')->constrained('payment_types')->cascadeOnDelete();
            $table->integer('payment_date')->comment('date of payment');
            $table->integer('payment_method')->comment('1: cash, 2: cheque, 3: bank transfer');
            $table->integer('payment_status')->comment('1: pending, 2: completed, 3: failed, 4: refunded');
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
