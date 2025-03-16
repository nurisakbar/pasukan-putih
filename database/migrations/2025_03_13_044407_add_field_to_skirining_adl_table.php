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
        Schema::table('skrining_adl', function (Blueprint $table) {
            $table->string('butuh_orang')->nullable();
            $table->string('pendamping_tetap')->nullable();
            $table->string('sasaran_home_service')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skrining_adl', function (Blueprint $table) {
            $table->dropColumn('butuh_orang');
            $table->dropColumn('pendamping_tetap');
            $table->dropColumn('sasaran_home_service');
        });
    }
};
