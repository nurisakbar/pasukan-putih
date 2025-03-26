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
            // $table->dropColumn('henti_layanan_kenaikan_aks');
            // $table->dropColumn('henti_layanan_meninggal');
            // $table->dropColumn('henti_layanan_menolak');
            // $table->dropColumn('henti_layanan_pindah_domisili');
            // $table->string('henti_layanan')->nullable();
            // $table->string('rujukan')->change();
            // $table->string('lanjut_kunjungan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            // $table->string('henti_layanan_kenaikan_aks')->nullable();
            // $table->string('henti_layanan_meninggal')->nullable();
            // $table->string('henti_layanan_menolak')->nullable();    
            // $table->string('henti_layanan_pindah_domisili')->nullable();
            // $table->dropColumn('henti_layanan');
            // $table->boolean('lanjut_kunjungan')->change();
            // $table->boolean('rujukan')->change();
        });
    }
};
