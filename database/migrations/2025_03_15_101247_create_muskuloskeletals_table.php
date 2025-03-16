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
        Schema::create('muskuloskeletals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('pasien_id');
            $table->index('pasien_id');
            $table->string('kontraktur')->nullable();
            $table->string('fraktur')->nullable();
            $table->string('nyeri_otot_tulang')->nullable();
            $table->string('drop_foot_lokasi')->nullable();
            $table->string('tremor')->nullable();
            $table->string('malaise_fatigue')->nullable();
            $table->string('atrofi')->nullable();
            $table->string('kekuatan_otot')->nullable();
            $table->string('postur_tidak_normal')->nullable();
            $table->string('alat_bantu')->nullable();
            $table->string('nyeri')->nullable();
            $table->string('tonus_otot')->nullable();
            $table->string('ekstremitas_atas')->nullable();
            $table->string('berdiri')->nullable();
            $table->string('berjalan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muskuloskeletals');
    }
};
