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
        Schema::create('neurosensoris', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('pasien_id');
            $table->index('pasien_id');
            // Fungsi Penglihatan
            $table->string('buram', 20)->nullable();
            $table->string('tidak_bisa_melihat', 20)->nullable();
            $table->string('alat_bantu_penglihatan', 20)->nullable();
            $table->string('visus', 20)->nullable();
            
            // Fungsi Peraba
            $table->string('kesemutan', 20)->nullable();
            $table->string('kebas', 20)->nullable();
            
            // Fungsi Pendengaran
            $table->string('kurang_jelas', 20)->nullable();
            $table->string('tuli', 20)->nullable();
            $table->string('tinnitus', 20)->nullable();
            
            // Fungsi Saraf
            $table->string('refleks_patologi', 20)->nullable();
            $table->string('disorientasi', 20)->nullable();
            $table->string('parese', 20)->nullable();
            $table->string('alat_bantu_saraf', 20)->nullable();
            $table->string('halusinasi', 20)->nullable();
            $table->string('disatria', 20)->nullable();
            $table->string('amnesia', 20)->nullable();
            $table->string('kekuatan_otot', 20)->nullable();
            $table->string('postur_tidak_normal', 20)->nullable();
            $table->string('nyeri', 20)->nullable();
            $table->string('sifat', 20)->nullable();
            $table->string('frekuensi', 20)->nullable();
            $table->string('lama', 20)->nullable();
            
            // Fungsi Perasa
            $table->string('mampu', 20)->nullable();
            $table->string('terganggu', 20)->nullable();
            
            // Kulit
            $table->string('memar', 20)->nullable();
            $table->string('laserasi', 20)->nullable();
            $table->string('ulserasi', 20)->nullable();
            $table->string('pus', 20)->nullable();
            $table->string('bulae_lepuh', 20)->nullable();
            $table->string('perdarahan_bawah', 20)->nullable();
            $table->string('krusta', 20)->nullable();
            $table->string('perubahan_warna', 20)->nullable();
            $table->string('luka_bakar_kulit', 20)->nullable();
            $table->string('decubitus_grade', 20)->nullable();
            $table->string('decubitus_lokasi', 20)->nullable();
            
            // Tidur dan Istirahat
            $table->string('susah_tidur', 20)->nullable();
            $table->string('waktu_tidur', 20)->nullable();
            $table->string('bantuan_obat', 20)->nullable();
            
            // Mental
            $table->string('cemas', 20)->nullable();
            $table->string('marah', 20)->nullable();
            $table->string('denial', 20)->nullable();
            $table->string('takut', 20)->nullable();
            $table->string('putus_asa', 20)->nullable();
            $table->string('depresi', 20)->nullable();
            $table->string('rendah_diri', 20)->nullable();
            $table->string('menarik_diri', 20)->nullable();
            $table->string('agresif', 20)->nullable();
            $table->string('perilaku_kekerasan', 20)->nullable();
            $table->string('tidak_mau_melihat_bagian_tubuh_yang_rusak', 20)->nullable();
            $table->string('respon_pasca_trauma', 20)->nullable();
            
            // Komunikasi dan Budaya
            $table->string('interaksi_keluarga', 20)->nullable();
            $table->string('berkomunikasi', 20)->nullable();
            $table->string('kegiatan_sosial', 20)->nullable();
            
            // Kebersihan Diri
            $table->string('gigi_dan_mulut_kotor', 20)->nullable();
            $table->string('kulit_kotor', 20)->nullable();
            $table->string('hidung_kotor', 20)->nullable();
            $table->string('telinga_kotor', 20)->nullable();
            $table->string('mata_kotor', 20)->nullable();
            $table->string('perial_genial_kotor', 20)->nullable();
            $table->string('kuku_kotor', 20)->nullable();
            $table->string('rambut_kepala_kotor', 20)->nullable();
            
            // Perawatan Diri Sehari-Hari
            $table->string('mandi', 20)->nullable();
            $table->string('berpakaian', 20)->nullable();
            $table->string('menyisir_rambut', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neurosensoris');
    }
};
