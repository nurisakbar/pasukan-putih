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
        Schema::create('pustus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_pustu');
            $table->integer('village_id');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('pustu_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pustus');
    }
};
