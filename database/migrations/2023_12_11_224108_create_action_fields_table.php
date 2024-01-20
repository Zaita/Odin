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
        Schema::create('action_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionnaire_question_id');
            $table->string("label");
            $table->string("action_type");
            $table->string("goto_question_title")->nullable();
            $table->json("tasks")->nullable();
            $table->integer("sort_order")->default(999);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_fields');
    }
};
