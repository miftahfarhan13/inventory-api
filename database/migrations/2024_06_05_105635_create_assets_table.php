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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->references('id')->on('users');
            $table->integer('category_id')->references('id')->on('categories');
            $table->integer('location_id')->references('id')->on('locations');
            $table->string('asset_code')->unique();
            $table->text('name');
            $table->text('brand')->nullable();
            $table->text('vendor')->nullable();
            $table->text('image_url')->nullable();
            $table->integer('price');
            $table->date('purchase_date')->nullable();
            $table->integer('routine_repair_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
