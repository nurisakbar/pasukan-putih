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
        Schema::table('health_forms', function (Blueprint $table) {
            $table->boolean('skilas_rujukan')->nullable()->comment('Apakah perlu rujukan');
            $table->text('skilas_rujukan_keterangan')->nullable()->comment('Keterangan rujukan');
            $table->text('skilas_hasil_tindakan_keperawatan')->nullable()->comment('Hasil Tindakan Keperawatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_forms', function (Blueprint $table) {
            $table->dropColumn(['skilas_rujukan', 'skilas_rujukan_keterangan', 'skilas_hasil_tindakan_keperawatan']);
        });
    }
};
