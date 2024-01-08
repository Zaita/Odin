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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['questionnaire', 'security_risk_questionnaire', 'risk_assessment', 'control_validation_audit'])->default('questionnaire');
            $table->boolean('lock_when_complete')->default(false);
            $table->boolean('approval_required')->default(false);
            $table->enum('risk_calculation', ['none', 'zaita_approx', 'highest_value'])->default('none');
            $table->unsignedBigInteger('approval_group')->nullable();
            $table->unsignedBigInteger('notification_group')->nullable();
            $table->unsignedBigInteger('task_object_id');
            $table->integer("sort_order")->default(999);
            $table->timestamps();

            $table->foreign('approval_group')->references('id')->on('groups')->onDelete('cascade');;
            $table->foreign('notification_group')->references('id')->on('groups')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
