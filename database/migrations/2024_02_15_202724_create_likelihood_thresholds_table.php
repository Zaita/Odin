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
        Schema::create('likelihood_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('operator');            
            $table->integer('value');
            $table->string('color');
            $table->integer('sort_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likelihood_thresholds');
    }
};
