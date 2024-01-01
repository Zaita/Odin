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
            $table->unsignedBigInteger('user_id');
            $table->string('submitter_name');
            $table->string('submitter_email');
            $table->string('pillar_name');
            $table->json('questionnaire_data');
            $table->json('answer_data');
            $table->string('product_name')->default("");
            $table->date('release_date')->nullable();
            $table->string('ticket_link')->default("");
            $table->string('business_owner')->nullable();

            // $table->json('email_status');
            // $table->string('ciso_approval_status');
            // $table->string('')
            // $table->json('ciso_approval_metadata');
            // $table->String('security_approval_status');
            // $table->json('security_approval_data');
            // $table->string('business_owner_approval_status');
            // $table->json('business_owner_approval_data');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
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
