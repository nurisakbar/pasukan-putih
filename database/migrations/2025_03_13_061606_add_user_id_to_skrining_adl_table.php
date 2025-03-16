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
            $table->uuid('pemeriksa_id');
            $table->index('pemeriksa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skrining_adl', function (Blueprint $table) {
            $table->dropColumn('pemeriksa_id');
        });
    }
};
