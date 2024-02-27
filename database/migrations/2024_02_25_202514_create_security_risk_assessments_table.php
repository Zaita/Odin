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
        Schema::create('security_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('security_catalogue_id')->nullable();
            $table->unsignedBigInteger('initial_risk_impact_id')->nullable();
            $table->string('name')->unique();
            $table->boolean('require_validation_audit')->default(false);
            $table->boolean('custom_likelihoods')->default(false);
            $table->boolean('custom_impacts')->default(false);
            $table->json('likelihood_thresholds')->nullable();
            $table->json('impact_thresholds')->nullable();
            $table->json('risk_matrix')->nullable();
            $table->timestamps();

            $table->foreign('security_catalogue_id')->references('id')->on('security_catalogues');
            $table->foreign('initial_risk_impact_id')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_risk_assessments');
    }
};
