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
        Schema::table('medical_specialities', function (Blueprint $table) {
            $table->enum('position', ['front', 'back'])->default('front')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_specialities', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
