<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add indexes to improve query performance, especially for sudinkes role
     * which uses JOIN queries across villages, districts, and regencies
     */
    public function up(): void
    {
        // Use DB::statement for better compatibility
        // Add index on villages.district_id for faster JOIN with districts
        try {
            DB::statement('CREATE INDEX villages_district_id_index ON villages (district_id)');
        } catch (\Exception $e) {
            // Index might already exist, skip
        }

        // Add index on districts.regency_id for faster JOIN with regencies
        try {
            DB::statement('CREATE INDEX districts_regency_id_index ON districts (regency_id)');
        } catch (\Exception $e) {
            // Index might already exist, skip
        }

        // Add index on pasiens.village_id for faster JOIN with villages
        try {
            DB::statement('CREATE INDEX pasiens_village_id_index ON pasiens (village_id)');
        } catch (\Exception $e) {
            // Index might already exist, skip
        }

        // Add index on pasiens.flag_sicarik for faster filtering
        try {
            DB::statement('CREATE INDEX pasiens_flag_sicarik_index ON pasiens (flag_sicarik)');
        } catch (\Exception $e) {
            // Index might already exist, skip
        }

        // Add index on visitings.pasien_id for faster JOIN with pasiens
        try {
            DB::statement('CREATE INDEX visitings_pasien_id_index ON visitings (pasien_id)');
        } catch (\Exception $e) {
            // Index might already exist, skip
        }

        // Add index on visitings.tanggal for faster date filtering
        try {
            DB::statement('CREATE INDEX visitings_tanggal_index ON visitings (tanggal)');
        } catch (\Exception $e) {
            // Index might already exist, skip
        }

        // Add composite index for common query patterns
        try {
            DB::statement('CREATE INDEX pasiens_village_flag_index ON pasiens (village_id, flag_sicarik)');
        } catch (\Exception $e) {
            // Index might already exist, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('DROP INDEX villages_district_id_index ON villages');
        } catch (\Exception $e) {
            // Index might not exist, skip
        }

        try {
            DB::statement('DROP INDEX districts_regency_id_index ON districts');
        } catch (\Exception $e) {
            // Index might not exist, skip
        }

        try {
            DB::statement('DROP INDEX pasiens_village_id_index ON pasiens');
        } catch (\Exception $e) {
            // Index might not exist, skip
        }

        try {
            DB::statement('DROP INDEX pasiens_flag_sicarik_index ON pasiens');
        } catch (\Exception $e) {
            // Index might not exist, skip
        }

        try {
            DB::statement('DROP INDEX pasiens_village_flag_index ON pasiens');
        } catch (\Exception $e) {
            // Index might not exist, skip
        }

        try {
            DB::statement('DROP INDEX visitings_pasien_id_index ON visitings');
        } catch (\Exception $e) {
            // Index might not exist, skip
        }

        try {
            DB::statement('DROP INDEX visitings_tanggal_index ON visitings');
        } catch (\Exception $e) {
            // Index might not exist, skip
        }
    }
};
