<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add advanced indexes for maximum performance
        Schema::table('visitings', function (Blueprint $table) {
            // Composite index for pasien_id and tanggal
            if (!$this->indexExists('visitings', 'idx_visitings_pasien_tanggal')) {
                $table->index(['pasien_id', 'tanggal'], 'idx_visitings_pasien_tanggal');
            }
            
            // Index for tanggal only
            if (!$this->indexExists('visitings', 'idx_visitings_tanggal')) {
                $table->index('tanggal', 'idx_visitings_tanggal');
            }
        });

        Schema::table('ttvs', function (Blueprint $table) {
            // Composite index for kunjungan_id and temperature
            if (!$this->indexExists('ttvs', 'idx_ttvs_kunjungan_temp')) {
                $table->index(['kunjungan_id', 'temperature'], 'idx_ttvs_kunjungan_temp');
            }
        });

        Schema::table('health_forms', function (Blueprint $table) {
            // Composite index for visiting_id and kunjungan_lanjutan
            if (!$this->indexExists('health_forms', 'idx_health_forms_visiting_lanjutan')) {
                $table->index(['visiting_id', 'kunjungan_lanjutan'], 'idx_health_forms_visiting_lanjutan');
            }
            
            // Index for henti_layanan
            if (!$this->indexExists('health_forms', 'idx_health_forms_henti_layanan')) {
                $table->index('henti_layanan', 'idx_health_forms_henti_layanan');
            }
        });

        // Add covering indexes for frequently accessed columns
        Schema::table('pasiens', function (Blueprint $table) {
            // Covering index for SiCarik queries
            if (!$this->indexExists('pasiens', 'idx_pasiens_covering_sicarik')) {
                $table->index(['flag_sicarik', 'village_id', 'deleted_at', 'id'], 'idx_pasiens_covering_sicarik');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitings', function (Blueprint $table) {
            $table->dropIndex('idx_visitings_pasien_tanggal');
            $table->dropIndex('idx_visitings_tanggal');
        });

        Schema::table('ttvs', function (Blueprint $table) {
            $table->dropIndex('idx_ttvs_kunjungan_temp');
        });

        Schema::table('health_forms', function (Blueprint $table) {
            $table->dropIndex('idx_health_forms_visiting_lanjutan');
            $table->dropIndex('idx_health_forms_henti_layanan');
        });

        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropIndex('idx_pasiens_covering_sicarik');
        });
    }

    /**
     * Check if index exists using raw SQL
     */
    private function indexExists($table, $indexName)
    {
        try {
            $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($result) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};