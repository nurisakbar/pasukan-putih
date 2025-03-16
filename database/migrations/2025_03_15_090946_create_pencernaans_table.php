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
        Schema::create('pencernaans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->uuid('pasien_id');
            $table->index('pasien_id');
            $table->string('mual')->nullable();
            $table->string('muntah')->nullable();
            $table->string('kembung')->nullable();
            $table->string('nafsu_makan')->nullable();
            $table->string('sulit_menelan')->nullable();
            $table->string('disfagia')->nullable();
            $table->string('bau_napas')->nullable();
            $table->string('kerusakan_gigi')->nullable();
            $table->string('distensi_abdomen')->nullable();
            $table->string('bising_usus')->nullable();
            $table->string('diare')->nullable();
            $table->string('hemoroid')->nullable();
            $table->string('stomatitis')->nullable();
            $table->string('warna_stomatitis')->nullable();
            $table->string('konstipasi')->nullable();
            $table->string('massa_abdomen')->nullable();
            $table->string('obat_pencahar')->nullable();
            $table->string('konsistensi')->nullable();
            $table->string('diet_khusus')->nullable();
            $table->string('kebiasaan_makan')->nullable();
            $table->string('alergi_makanan')->nullable();
            $table->string('alat_bantu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencernaans');
    }
};
