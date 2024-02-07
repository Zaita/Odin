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
        Schema::create('security_control_risk_weights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('security_control_id');
            $table->unsignedBigInteger('risk_id');
            $table->integer('likelihood')->unsigned();
            $table->integer('likelihood_penalty')->unsigned();
            $table->integer('impact')->unsigned();
            $table->integer('impact_penalty')->unsigned();
            $table->timestamps();

            $table->foreign('security_control_id')->references('id')->on('security_controls');
            $table->foreign('risk_id')->references('id')->on('risks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_control_risk_weights');
    }
};
