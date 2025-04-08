<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('health_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('user_id');
            $table->index('user_id');
            $table->uuid('visiting_id');
            $table->index('visiting_id');
            
            // Riwayat Penyakit
            $table->boolean('no_disease')->default(false);
            $table->json('diseases')->nullable();
            $table->string('cancer_type')->nullable();
            $table->string('lung_disease_type')->nullable();
            
            // Skrining ILP
            $table->boolean('screening_obesity')->nullable();
            $table->string('obesity_status')->nullable();
            $table->boolean('screening_hypertension')->nullable();
            $table->string('hypertension_status')->nullable();
            $table->boolean('screening_diabetes')->nullable();
            $table->string('diabetes_status')->nullable();
            $table->boolean('screening_stroke')->nullable();
            $table->string('stroke_status')->nullable();
            $table->boolean('screening_heart_disease')->nullable();
            $table->string('heart_disease_status')->nullable();
            $table->boolean('screening_breast_cancer')->nullable();
            $table->string('breast_cancer_status')->nullable();
            $table->boolean('screening_cervical_cancer')->nullable();
            $table->string('cervical_cancer_status')->nullable();
            $table->boolean('screening_lung_cancer')->nullable();
            $table->string('lung_cancer_status')->nullable();
            $table->boolean('screening_colorectal_cancer')->nullable();
            $table->string('colorectal_cancer_status')->nullable();
            $table->boolean('screening_mental_health')->nullable();
            $table->string('mental_health_status')->nullable();
            $table->boolean('screening_ppok')->nullable();
            $table->string('ppok_status')->nullable();
            $table->boolean('screening_tbc')->nullable();
            $table->string('tbc_status')->nullable();
            $table->boolean('screening_vision')->nullable();
            $table->string('vision_status')->nullable();
            $table->boolean('screening_hearing')->nullable();
            $table->string('hearing_status')->nullable();
            $table->boolean('screening_fitness')->nullable();
            $table->string('fitness_status')->nullable();
            $table->boolean('screening_dental')->nullable();
            $table->string('dental_status')->nullable();
            $table->boolean('screening_elderly')->nullable();
            $table->string('elderly_status')->nullable();
            
            // Skor AKS
            $table->string('skor_aks')->nullable();
            
            // Jenis gangguan fungsional
            $table->boolean('gangguan_komunikasi')->nullable();
            $table->boolean('kesulitan_makan')->nullable();
            $table->boolean('gangguan_fungsi_kardiorespirasi')->nullable();
            $table->boolean('gangguan_fungsi_berkemih')->nullable();
            $table->boolean('gangguan_mobilisasi')->nullable();
            $table->boolean('gangguan_partisipasi')->nullable();
            
            // Perawatan Umum Yang Dilakukan
            $table->boolean('perawatan_hygiene')->nullable();
            $table->boolean('perawatan_skin_care')->nullable();
            $table->boolean('perawatan_environment')->nullable();
            $table->boolean('perawatan_welfare')->nullable();
            $table->boolean('perawatan_sunlight')->nullable();
            $table->boolean('perawatan_communication')->nullable();
            $table->boolean('perawatan_recreation')->nullable();
            $table->boolean('perawatan_penamtauan_obat')->nullable();
            $table->boolean('perawatan_ibadah')->nullable();
            
            // Perawatan Khusus Yang Dilakukan
            $table->boolean('perawatan_membantu_warga')->nullable();
            $table->boolean('perawatan_monitoring_gizi')->nullable();
            $table->boolean('perawatan_membantu_bak_bab')->nullable();
            $table->boolean('perawatan_menangani_gangguan')->nullable();
            $table->boolean('perawatan_pengelolaan_stres')->nullable();
            
            // Perawatan
            $table->text('perawatan')->nullable();
            
            // Keluaran perawatan
            $table->tinyInteger('keluaran')->nullable(); // 1: Meningkat, 2: Tetap, 3: Menurun
            $table->string('keterangan')->nullable();
            
            // Pembinaan keluarga
            $table->string('pembinaan')->nullable();
            
            // Tingkat Kemandirian Keluarga
            $table->json('kemandirian')->nullable();
            $table->string('tingkat_kemandirian')->nullable();
            
            // Kunjungan Lanjutan
            $table->string('kunjungan_lanjutan')->nullable();
            $table->text('permasalahan_lanjutan')->nullable();
            $table->date('tanggal_kunjungan')->nullable();
            
            $table->timestamps();

            $table->foreign('visiting_id')->references('id')->on('visitings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_forms');
    }
};
