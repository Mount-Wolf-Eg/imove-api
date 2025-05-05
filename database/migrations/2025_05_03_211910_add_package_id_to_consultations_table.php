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
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->constrained('packages')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->after('package_id')->constrained('subscriptions')->cascadeOnDelete()->comment('Subscription ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');

            $table->dropForeign(['subscription_id']);
            $table->dropColumn('subscription_id');
        });
    }
};
