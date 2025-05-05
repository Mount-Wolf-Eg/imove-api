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
        Schema::create('patient_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            $table->integer('week')->nullable();
            $table->integer('day')->nullable();
            $table->integer('degree_of_pain')->default(0)->nullable()->comment('From 1 to 10');
            $table->integer('extent_of_improvement')->default(0)->nullable()->comment('From 1 to 10');
            $table->string('comments')->nullable();
            $table->timestamp('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_sessions');
    }
};
