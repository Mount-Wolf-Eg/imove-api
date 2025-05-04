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
        Schema::create('patient_session_exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('patient_sessions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('exercise_id')->nullable();
            $table->foreign('exercise_id')->references('id')->on('exercises')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('sets')->nullable();
            $table->integer('break_between_sets')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('rep')->nullable();
            $table->integer('hold_duration')->nullable();
            $table->string('comments')->nullable();
            $table->integer('ease_of_exercise')->default(0)->nullable()->comment('From 1 to 10');
            $table->string('reason_for_overtaking')->nullable();
            $table->boolean('complete_sets')->default(false);
            $table->integer('patient_total_sets')->default(0)->nullable()->comment('From 1 to 10');
            $table->integer('patient_total_reps')->default(0)->nullable()->comment('From 1 to 10');
            $table->json('patient_exercise_repetitions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_session_exercises');
    }
};
