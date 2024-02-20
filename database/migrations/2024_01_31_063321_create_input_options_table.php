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
        Schema::create('input_options', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('input_field_id')->unsigned();
            $table->string('label');
            $table->string('value');
            $table->json('risks')->nullable();
            $table->integer('sort_order')->unsigned()->default(999);
            $table->timestamps();
            
            $table->foreign('input_field_id')->references('id')->on('input_fields')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('input_options');
    }
};
