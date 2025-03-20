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
            $table->string('village_id')->nullable()->change(); 
            $table->string('district_id')->nullable()->change();
            $table->string('regency_id')->nullable()->change();
            $table->string('province_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->string('village_id')->nullable(false)->change(); 
            $table->string('district_id')->nullable(false)->change();
            $table->string('regency_id')->nullable(false)->change();
            $table->string('province_id')->nullable(false)->change();
        });
    }
};
