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
        Schema::create('submission_approval_flow_stages', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('submission_id');          
          $table->string('type');
          $table->string('target')->nullable();
          $table->string('approval_type');
          $table->boolean('wait_for_approval')->default(true);
          $table->integer('stage_order');
          $table->unsignedBigInteger('assigned_to_user_id')->nullable();
          $table->string('assigned_to_user_name')->nullable();
          $table->string('assigned_to_user_email')->nullable();
          $table->unsignedBigInteger('approved_by_user_id')->nullable();
          $table->string('approved_by_user_name')->nullable();
          $table->string('approved_by_user_email')->nullable();
          $table->string('status')->nullable();
          $table->timestamps();

          $table->foreign('submission_id')->references('id')->on('submissions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_approval_flow_stages');
    }
};
