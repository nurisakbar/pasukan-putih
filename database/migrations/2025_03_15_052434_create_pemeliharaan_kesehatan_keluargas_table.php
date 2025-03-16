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
        Schema::create('pemeliharaan_kesehatan_keluargas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('pasien_id');
            $table->index('pasien_id');
            $table->string('perhatian_keluarga')->nullable();
            $table->string('mengetahui_masalah_kesehatan')->nullable();
            $table->string('penyebab_masalah_kesehatan')->nullable();
            $table->string('akibat_masalah_kesehatan')->nullable();
            $table->string('keyakinan_keluarga')->nullable();
            $table->string('upaya_peningkatan_kesehatan')->nullable();
            $table->string('upaya_peningkatan_kesehatan_deskripsi')->nullable();
            $table->string('kebutuhan_pengobatan')->nullable();
            $table->string('merawat_anggota_keluarga')->nullable();
            $table->string('melakukan_pencegahan_masalah')->nullable();
            $table->string('mendukung_kesehatan')->nullable();
            $table->string('memanfaatkan_sumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeliharaan_kesehatan_keluargas');
    }
};
