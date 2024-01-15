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
            $table->json('pillar_data');
            $table->json('questionnaire_data');
            $table->json('answer_data');
            $table->string('product_name')->default("");
            $table->date('release_date')->nullable();
            $table->string('ticket_link')->default("");
            $table->string('business_owner')->nullable();
            $table->unsignedBigInteger('approval_stage')->default(0);
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
