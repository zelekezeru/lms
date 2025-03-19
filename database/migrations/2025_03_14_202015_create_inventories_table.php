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
            $table->foreignId('category_id')
                ->constrained('inventory_categories')
                ->onDelete('cascade');
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('inventory_suppliers')
                ->onDelete('cascade');

            $table->string('name', 100);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['ACTIVE', 'IN_ACTIVE']);
            $table->timestamps();
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
