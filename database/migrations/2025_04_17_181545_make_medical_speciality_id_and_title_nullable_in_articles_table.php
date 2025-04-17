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
        Schema::table('articles', function (Blueprint $table) {
            $table->longText('title')->nullable()->change();

            $table->dropForeign(['medical_speciality_id']);
            $table->unsignedBigInteger('medical_speciality_id')->nullable()->change();

            $table->foreign('medical_speciality_id')
                ->references('id')
                ->on('medical_specialities')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->longText('title')->nullable(false)->change();
            
            $table->dropForeign(['medical_speciality_id']);
            $table->unsignedBigInteger('medical_speciality_id')->nullable(false)->change();
            $table->foreign('medical_speciality_id')
                ->references('id')
                ->on('medical_specialities');
        });
    }
};
