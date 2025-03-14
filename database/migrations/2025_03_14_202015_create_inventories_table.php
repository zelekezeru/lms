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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name', 100);
            $table->unsignedBigInteger('category_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
