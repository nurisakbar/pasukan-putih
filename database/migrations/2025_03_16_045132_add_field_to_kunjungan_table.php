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
        Schema::table('kunjungans', function (Blueprint $table) {
            $table->integer('skor_aks_data_sasaran')->nullable();
            $table->integer('skor_aks')->nullable();
            $table->boolean('lanjut_kunjungan')->default(false);
            $table->date('rencana_kunjungan_lanjutan')->nullable();
            $table->boolean('henti_layanan_kenaikan_aks')->default(false);
            $table->boolean('henti_layanan_meninggal')->default(false);
            $table->boolean('henti_layanan_menolak')->default(false);
            $table->boolean('henti_layanan_pindah_domisili')->default(false);
            $table->boolean('rujukan')->default(false);
            $table->boolean('konversi_data_ke_sasaran_kunjungan_lanjutan')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            $table->dropColumn('skor_aks_data_sasaran');
            $table->dropColumn('skor_aks');
            $table->dropColumn('lanjut_kunjungan');
            $table->dropColumn('rencana_kunjungan_lanjutan');
            $table->dropColumn('henti_layanan_kenaikan_aks');
            $table->dropColumn('henti_layanan_meninggal');
            $table->dropColumn('henti_layanan_menolak');
            $table->dropColumn('henti_layanan_pindah_domisili');
            $table->dropColumn('rujukan');
            $table->dropColumn('konversi_data_ke_sasaran_kunjungan_lanjutan');
        });
    }
};
