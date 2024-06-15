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
        Schema::create('asset_improvements', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->references('id')->on('users');
            $table->integer('approved_by')->references('id')->on('users');
            $table->integer('asset_id')->references('id')->on('assets');
            $table->text('type');
            $table->text('status')->nullable();
            $table->longText('description');
            $table->text('reporter');
            $table->text('contact_reporter')->nullable();
            $table->text('contact_technician')->nullable();
            $table->integer('improvement_price');
            $table->string('additional_document')->nullable();
            $table->date('report_date')->nullable();
            $table->date('validation_by_laboratory_date')->nullable();
            $table->date('repair_time_plan_date')->nullable();
            $table->date('actual_repair_start_date')->nullable();
            $table->date('actual_repair_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_improvements');
    }
};
