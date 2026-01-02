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
            // Drop kunjungan_id if exists
            if (Schema::hasColumn('skrining_adl', 'kunjungan_id')) {
                $table->dropIndex(['kunjungan_id']);
                $table->dropColumn('kunjungan_id');
            }
            
            // Add visiting_id if not exists
            if (!Schema::hasColumn('skrining_adl', 'visiting_id')) {
                $table->uuid('visiting_id')->nullable();
                $table->index('visiting_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skrining_adl', function (Blueprint $table) {
            // Drop visiting_id if exists
            if (Schema::hasColumn('skrining_adl', 'visiting_id')) {
                $table->dropIndex(['visiting_id']);
                $table->dropColumn('visiting_id');
            }
            
            // Add back kunjungan_id
            $table->uuid('kunjungan_id');
            $table->index('kunjungan_id');
        });
    }
};
