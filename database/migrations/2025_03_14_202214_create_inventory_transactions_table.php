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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('transaction_type', ['issue', 'return'])->nullable();
            $table->integer('quantity');
            $table->enum('status', ['pending', 'completed', 'cancelled']);
            $table->timestamp('transaction_date');
            $table->timestamp('return_date')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('item_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
