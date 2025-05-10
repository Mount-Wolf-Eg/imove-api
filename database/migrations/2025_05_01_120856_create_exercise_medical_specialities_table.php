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
        Schema::create('exercise_medical_specialities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id')->nullable();
            $table->foreign('exercise_id')->references('id')->on('exercises')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('medical_speciality_id')->nullable()->index();
            $table->foreign('medical_speciality_id')->references('id')->on('medical_specialities')->cascadeOnUpdate()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_medical_specialities');
    }
};
