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
            // Add composite indexes for better query performance
            $table->index(['flag_sicarik', 'village_id'], 'idx_pasiens_flag_village');
            $table->index(['user_id', 'flag_sicarik'], 'idx_pasiens_user_flag');
            $table->index(['flag_sicarik', 'deleted_at'], 'idx_pasiens_flag_deleted');
            
            // Add index for village_id if not exists
            if (!$this->indexExists('pasiens', 'pasiens_village_id_index')) {
                $table->index('village_id');
            }
        });

        // Add indexes to related tables for join performance
        Schema::table('villages', function (Blueprint $table) {
            if (!$this->indexExists('villages', 'villages_district_id_index')) {
                $table->index('district_id');
            }
        });

        Schema::table('districts', function (Blueprint $table) {
            if (!$this->indexExists('districts', 'districts_regency_id_index')) {
                $table->index('regency_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropIndex('idx_pasiens_flag_village');
            $table->dropIndex('idx_pasiens_user_flag');
            $table->dropIndex('idx_pasiens_flag_deleted');
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        $indexes = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table);
        
        return array_key_exists($indexName, $indexes);
    }
};