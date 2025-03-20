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
        Schema::dropIfExists('ttvs'); // Hapus tabel lama

        Schema::create('ttvs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('kunjungan_id');
            $table->index('kunjungan_id');
            $table->string('temperature')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->string('respiration')->nullable();
            $table->string('bmi')->nullable();
            $table->string('bmi_category')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->integer('pulse')->nullable();
            $table->string('oxygen_saturation')->nullable();
            $table->string('blood_sugar')->nullable();
            $table->string('uric_acid')->nullable();
            $table->string('tcho')->nullable();
            $table->string('triglyceride')->nullable();
            $table->string('high_density_protein')->nullable();
            $table->string('low_density_protein')->nullable();
            $table->string('hemoglobin')->nullable();
            $table->string('jaundice')->nullable();
            $table->string('w_waist')->nullable();
            $table->string('w_bust')->nullable();
            $table->string('w_hip')->nullable();
            $table->integer('fetal_heart')->nullable();
            $table->string('ecg')->nullable();
            $table->string('ultrasound')->nullable();
            $table->string('white_corpuscle')->nullable();
            $table->string('red_corpuscle')->nullable();
            $table->string('nitrous_acid')->nullable();
            $table->string('ketone_body')->nullable();
            $table->string('urobilinogen')->nullable();
            $table->string('bilirubin')->nullable();
            $table->string('protein')->nullable();
            $table->string('glucose')->nullable();
            $table->string('ph')->nullable();
            $table->string('vitamin_c')->nullable();
            $table->string('creatinine')->nullable();
            $table->string('proportion')->nullable();
            $table->string('albumin')->nullable();
            $table->string('calcium')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ttvs');
    }
};
