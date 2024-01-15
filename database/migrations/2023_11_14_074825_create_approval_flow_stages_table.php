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
        Schema::create('approval_flow_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_flow_id');
            $table->integer('stage_order');
            $table->string('type');
            $table->string('target')->nullable();
            $table->string('approval_type');
            $table->boolean('wait_for_approval')->default(true);
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->foreign('approval_flow_id')->references('id')->on('approval_flows')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_flow_stages');        
    }
};
