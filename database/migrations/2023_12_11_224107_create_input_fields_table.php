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
        Schema::create('input_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionnaire_question_id');
            $table->string("label");
            $table->boolean("required");
            $table->string("input_type");
            $table->integer("min_length")->nullable();
            $table->integer("max_length")->nullable();
            $table->string("placeholder")->nullable();
            $table->boolean("product_name")->default(false);
            $table->boolean("business_owner")->default(false);
            $table->boolean("release_date")->nullable();
            $table->boolean('ticket_url')->nullable();
            $table->integer("sort_order")->default(999);
            $table->json('config')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('input_fields');
    }
};
