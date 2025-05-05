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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete()->comment('Coupon ID');

            $table->string('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->integer('num_of_sessions')->nullable();
            $table->integer('used_num_of_sessions')->default(0);

            $table->integer('payment_type')->nullable()->comment('Payment type');
            
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('app_amount', 10, 2)->default(0)->comment('App amount');
            $table->decimal('tax_amount', 10, 2)->default(0)->comment('Tax amount');
            $table->decimal('coupon_discount', 10, 2)->default(0)->comment('Coupon discount');
            $table->decimal('doctor_amount', 10, 2)->default(0)->comment('Doctor amount');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('Total amount');

            $table->boolean('is_paid')->default(false)->comment('Is paid');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
