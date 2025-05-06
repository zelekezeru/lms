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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('credit_hours');
            $table->integer('duration');
            $table->text('description')->nullable();

            $table->boolean('is_training')->default(0);
            $table->boolean('status')->default(1);
            $table->boolean('is_deleted')->default(0);
            $table->boolean('is_published')->default(0);
            $table->boolean('is_approved')->default(1);
            $table->boolean('is_completed')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
