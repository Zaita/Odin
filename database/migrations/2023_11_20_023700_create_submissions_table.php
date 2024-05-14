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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('uuid');
            $table->unsignedBigInteger('submitter_id');
            $table->string('submitter_name');
            $table->string('submitter_email');
            $table->string('pillar_name');
            $table->enum('type', ['questionnaire', 'risk_questionnaire'])->default('questionnaire');            
            $table->enum('risk_calculation', ['none', 'zaita_approx', 'highest_value'])->default('none');
            $table->json('pillar_data');
            $table->json('questionnaire_data');
            $table->json('answer_data');
            $table->json('risks');
            $table->json('risk_data');
            $table->string('product_name')->default("");
            $table->datetime('release_date')->nullable();
            $table->string('ticket_link')->default("");
            $table->string('business_owner')->nullable();
            $table->unsignedBigInteger('approval_stage')->default(0);
            $table->timestamp('approved_at', 0)->nullable();
            $table->timestamps();            

            $table->foreign('submitter_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
