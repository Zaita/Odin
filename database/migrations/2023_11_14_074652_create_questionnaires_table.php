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
      Schema::create('questionnaires', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->enum('type', ['questionnaire', 'risk_questionnaire'])->default('questionnaire');
        $table->enum('risk_calculation', ['none', 'zaita_approx', 'highest_value'])->default('none');
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('questionnaires');
    }
};

