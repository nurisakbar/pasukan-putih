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
        Schema::create('ttvs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('kunjungan_id');
            $table->index('kunjungan_id');
            $table->string('blood_pressure')->nullable(); // Tekanan Darah
            $table->integer('pulse')->nullable(); // Nadi
            $table->integer('respiration')->nullable(); // Pernapasan
            $table->decimal('temperature', 4, 1)->nullable(); // Suhu
            $table->integer('oxygen_saturation')->nullable(); // Saturasi Oksigen
            
            // Antropometri
            $table->decimal('weight', 5, 2)->nullable(); // Berat Badan
            $table->decimal('height', 5, 1)->nullable(); // Tinggi Badan
            $table->decimal('knee_height', 5, 1)->nullable(); // Tinggi Lutut
            $table->decimal('sitting_height', 5, 1)->nullable(); // Tinggi Duduk
            $table->decimal('arm_span', 5, 1)->nullable(); // Panjang Depa
            
            // BMI (calculated field)
            $table->decimal('bmi', 5, 2)->nullable(); // Indeks Massa Tubuh
            $table->string('bmi_category')->nullable(); // Kategori IMT
            
            // Timestamps
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
