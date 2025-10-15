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
        Schema::table('health_forms', function (Blueprint $table) {
            // SKILAS - Simple checkbox fields (Ya/Tidak)
            $table->boolean('skilas_kognitif')->nullable()->comment('Penurunan Kognitif - Ada gangguan');
            $table->boolean('skilas_mobilisasi')->nullable()->comment('Keterbatasan Mobilisasi - Tidak dapat berdiri 5x dalam 14 detik');
            $table->boolean('skilas_malnutrisi_berat_badan')->nullable()->comment('Berat badan turun >3kg dalam 3 bulan');
            $table->boolean('skilas_malnutrisi_nafsu_makan')->nullable()->comment('Hilang nafsu makan/kesulitan makan');
            $table->boolean('skilas_malnutrisi_lila')->nullable()->comment('LILA <21cm');
            $table->boolean('skilas_penglihatan')->nullable()->comment('Gangguan Penglihatan - Perlu tes lebih lanjut');
            $table->boolean('skilas_pendengaran')->nullable()->comment('Gangguan Pendengaran - Tidak dapat mendengar bisikan');
            $table->boolean('skilas_depresi_sedih')->nullable()->comment('Perasaan sedih/tertekan/putus asa');
            $table->boolean('skilas_depresi_minat')->nullable()->comment('Kehilangan minat/kesenangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_forms', function (Blueprint $table) {
            // Drop SKILAS fields
            $table->dropColumn([
                'skilas_kognitif',
                'skilas_mobilisasi',
                'skilas_malnutrisi_berat_badan',
                'skilas_malnutrisi_nafsu_makan',
                'skilas_malnutrisi_lila',
                'skilas_penglihatan',
                'skilas_pendengaran',
                'skilas_depresi_sedih',
                'skilas_depresi_minat',
            ]);
        });
    }
};
