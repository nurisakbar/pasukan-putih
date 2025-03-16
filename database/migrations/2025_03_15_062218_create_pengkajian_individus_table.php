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
        Schema::create('pengkajian_individus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('pasien_id');
            $table->index('pasien_id');
            $table->string('kesadaran')->nullable();
            $table->string('gcs')->nullable();
            $table->string('sistole')->nullable();
            $table->string('diastole')->nullable();
            $table->string('pernapasan')->nullable();
            $table->string('suhu')->nullable();
            $table->string('nadi')->nullable();
            $table->string('takikardi')->nullable();
            $table->string('bradikardia')->nullable();
            $table->string('tubuhHangat')->nullable();
            $table->string('menggigil')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengkajian_individus');
    }
};
