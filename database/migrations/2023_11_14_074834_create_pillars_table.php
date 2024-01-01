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
        Schema::create('pillars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('caption');
            $table->string('icon')->default('none');                       
            $table->text('key_information');
            $table->enum('risk_calculation', ['none', 'zaita_approx', 'highest_value'])->default('none');
            $table->boolean('auto_approve')->default(false);
            $table->boolean('auto_approve_no_tasks')->default(false);
            $table->boolean('submission_expires')->default(false);
            $table->unsignedInteger('expire_after_days')->default(0);                        
            $table->unsignedInteger('sort_order')->default(9999);
            $table->unsignedBigInteger('questionnaire_id');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pillars');        
    }
};
