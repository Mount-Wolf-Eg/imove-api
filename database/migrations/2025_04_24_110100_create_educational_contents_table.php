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
        Schema::create('educational_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
            $table->longText('title');
            $table->longText('content');
            $table->unsignedBigInteger('medical_speciality_id')->nullable()->index();
            $table->foreign('medical_speciality_id')
                ->references('id')
                ->on('medical_specialities')
                ->nullOnDelete();
            $table->date('publish_date')->nullable();
            $table->integer('views')->default(0);
            $table->integer('shares')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_contents');
    }
};
