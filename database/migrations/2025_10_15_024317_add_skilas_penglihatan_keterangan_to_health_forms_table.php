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
            $table->text('skilas_penglihatan_keterangan')->nullable()->after('skilas_penglihatan')->comment('Keterangan hasil tes penglihatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_forms', function (Blueprint $table) {
            $table->dropColumn('skilas_penglihatan_keterangan');
        });
    }
};
