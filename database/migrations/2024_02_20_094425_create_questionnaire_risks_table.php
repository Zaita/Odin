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
        Schema::create('questionnaire_risks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("questionnaire_id");
            $table->string("name");
            $table->text("description")->nullable();
            $table->timestamps();
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_risks');
    }
};
