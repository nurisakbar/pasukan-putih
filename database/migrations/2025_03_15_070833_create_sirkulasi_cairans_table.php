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
        Schema::create('sirkulasi_cairans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('pasien_id');
            $table->index('pasien_id');
            $table->string('edema')->nullable();
            $table->string('bunyi_jantung')->nullable();
            $table->string('asites')->nullable();
            $table->string('akral_dingin')->nullable();
            $table->string('tanda_perdarahan')->nullable();
            $table->string('tanda_anemia')->nullable();
            $table->string('tanda_dehidrasi')->nullable();
            $table->string('pusing')->nullable();
            $table->string('kesemutan')->nullable();
            $table->string('berkeringat')->nullable();
            $table->string('rasa_haus')->nullable();
            $table->string('pengisian_kapiler')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sirkulasi_cairans');
    }
};
