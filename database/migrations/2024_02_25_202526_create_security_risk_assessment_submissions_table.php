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
        Schema::create('security_risk_assessment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_submission_id');
            $table->unsignedBigInteger('initial_risk_id');
            $table->boolean('require_validation_audit');
            $table->json('likelihood_thresholds');
            $table->json('impact_thresholds');
            $table->json('risk_matrix');
            $table->timestamps();
            
            $table->foreign('task_submission_id')->references('id')->on('task_submissions')->onDelete('restrict');
            $table->foreign('initial_risk_id')->references('id')->on('task_submissions')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_risk_assessment_submissions');
    }
};
