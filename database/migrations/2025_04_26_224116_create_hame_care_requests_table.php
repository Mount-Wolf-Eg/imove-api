<?php

use App\Constants\ConsultationStatusConstants;
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
        Schema::create('hame_care_requests', function (Blueprint $table) {
            $table->id();
            // $table->tinyInteger('status')->default(ConsultationStatusConstants::PENDING->value);
            $table->tinyInteger('status')->default(1)->comment('1 => The request is being reviewed, 2 => Visited , 3 => Reject');
            $table->unsignedBigInteger('patient_id')->nullable()->index();
            $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
            $table->unsignedBigInteger('city_id')->nullable()->index();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            $table->unsignedBigInteger('medical_speciality_id')->nullable()->index();
            $table->foreign('medical_speciality_id')->references('id')->on('medical_specialities')->nullOnDelete();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hame_care_requests');
    }
};
