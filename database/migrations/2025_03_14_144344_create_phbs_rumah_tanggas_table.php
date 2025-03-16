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
        Schema::create('phbs_rumah_tanggas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->string('ibu_nifas')->nullable();
            $table->string('ada_bayi')->nullable();
            $table->string('ada_balita')->nullable();
            $table->string('air_bersih')->nullable();
            $table->string('mencuci_tangan')->nullable();
            $table->string('buang_sampah')->nullable();
            $table->string('menjaga_lingkungan_rumah')->nullable();
            $table->string('konsumsi_lauk')->nullable();
            $table->string('gunakan_jamban')->nullable();
            $table->string('jentik_dirumah')->nullable();
            $table->string('makan_buah_sayur')->nullable();
            $table->string('aktivitas_fisik')->nullable();
            $table->string('merokok_dalam_rumah')->nullable();
            $table->uuid('pasien_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phbs_rumah_tanggas');
    }
};
