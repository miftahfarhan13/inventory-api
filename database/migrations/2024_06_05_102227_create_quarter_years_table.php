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
        Schema::create('quarter_years', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->references('id')->on('users');
            $table->text('year');
            $table->date('start_tw_1');
            $table->date('end_tw_1');
            $table->date('start_tw_2');
            $table->date('end_tw_2');
            $table->date('start_tw_3');
            $table->date('end_tw_3');
            $table->date('start_tw_4');
            $table->date('end_tw_4');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarter_years');
    }
};
