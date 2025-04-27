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
        Schema::rename('hame_care_requests', 'home_care_requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('home_care_requests', 'hame_care_requests');
    }
};

