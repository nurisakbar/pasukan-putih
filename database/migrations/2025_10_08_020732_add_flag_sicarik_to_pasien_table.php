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
        Schema::table('pasiens', function (Blueprint $table) {
            $table->boolean('flag_sicarik')->default(false);
            $table->string('nomor_whatsapp')->nullable();
            $table->string('nama_pendamping')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn('flag_sicarik');
            $table->dropColumn('nomor_whatsapp');
            $table->dropColumn('nama_pendamping');
        });
    }
};
