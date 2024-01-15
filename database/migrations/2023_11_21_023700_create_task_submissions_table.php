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
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('uuid');
            $table->unsignedBigInteger('submission_id');
            $table->string('submitter_name')->nullable();
            $table->string('submitter_email')->nullable();
            $table->string('task_type');
            $table->json('task_data');
            $table->json('answer_data');
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->string('approver_name')->nullable();
            $table->string('result')->nullable();            
            $table->timestamps();

            $table->foreign('submission_id')->references('id')->on('submissions');
            $table->foreign('approver_id')->references('id')->on('users');            
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