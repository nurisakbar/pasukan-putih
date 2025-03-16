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
        Schema::create('perkemihans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('pasien_id');
            $table->index('pasien_id');
            $table->string('pola_bak')->nullable();
            $table->string('volume')->nullable();
            $table->string('hematuri')->nullable();
            $table->string('poliuria')->nullable();
            $table->string('oliguria')->nullable();
            $table->string('disuria')->nullable();
            $table->string('inkontinensia')->nullable();
            $table->string('retensi')->nullable();
            $table->string('nyeri_bak')->nullable();
            $table->string('kemampuan_bak')->nullable();
            $table->string('alat_bantu_bak')->nullable();
            $table->string('obat_bak')->nullable();
            $table->string('kemampuan_bab')->nullable();
            $table->string('alat_bantu_bab')->nullable();
            $table->string('obat_bab')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkemihans');
    }
};
