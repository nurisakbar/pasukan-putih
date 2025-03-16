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
        Schema::create('pasiens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('id');
            $table->string('name');
            $table->string('nik')->unique();
            $table->string('alamat');
            $table->string('jenis_kelamin');
            $table->string('village_id');
            $table->string('district_id');
            $table->string('regency_id');
            $table->string('province_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
