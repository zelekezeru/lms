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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('language')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('duration')->nullable();
            $table->foreignId('study_mode_id')->nullable()->constrained('study_modes')->cascadeOnDelete();
            $table->foreignId('payment_category_id')->nullable()->constrained('payment_categories')->cascadeOnDelete();

            $table->boolean('is_active')->default(true);
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
