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
        Schema::create('security_controls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('security_catalogue_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('implementation_guidance')->nullable();
            $table->text('implementation_evidence')->nullable();
            $table->text('audit_guidance')->nullable();
            $table->text('reference_standards')->nullable();
            $table->string('control_owner_name')->nullable();
            $table->string('control_owner_email')->nullable();
            $table->text('tags')->nullable();
            $table->timestamps();

            $table->foreign('security_catalogue_id')->references('id')->on('security_catalogues');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_controls');
    }
};
