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
        Schema::create('kondisi_rumahs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->string('ventilasi')->nullable();
            $table->string('pencahayaan')->nullable();
            $table->string('saluran_limbah')->nullable();
            $table->string('sumber_air')->nullable();
            $table->string('jamban')->nullable();
            $table->string('tempat_sampah')->nullable();
            $table->uuid('pasien_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kondisi_rumahs');
    }
};
