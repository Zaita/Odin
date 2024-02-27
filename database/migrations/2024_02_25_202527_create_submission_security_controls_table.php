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
        Schema::create('submission_security_controls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id');
            $table->unsignedBigInteger('sra_submission_id');
            $table->string('security_catalogue_name');
            $table->string('name');
            $table->json('risk_weights');
            $table->text('description')->nullable();
            $table->text('implementation_guidance')->nullable();
            $table->text('implementation_evidence')->nullable();
            $table->text('audit_guidance')->nullable();
            $table->text('audit_method')->nullable();
            $table->text('audit_findings')->nullable();
            $table->text('audit_recommendations')->nullable();
            $table->text('reference_standards')->nullable();
            $table->string('control_owner_name')->nullable();
            $table->string('control_owner_email')->nullable();            
            $table->text('tags')->nullable();
            $table->enum('implementation_status', ['not_applicable', 'not_implemented', 'planned', 'implemented'])->default('not_implemented');
            
            $table->timestamps();

            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('restrict');
            $table->foreign('sra_submission_id')->references('id')->on('security_risk_assessment_submissions')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_security_controls');
    }
};
