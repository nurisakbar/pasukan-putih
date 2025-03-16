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
        Schema::create('skrining_adl', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kunjungan_id');
            $table->index('kunjungan_id');
            $table->uuid('pasien_id');
            $table->index('pasien_id');
            $table->integer('bab_control')->comment('Mengendalikan rangsangan BAB');
            $table->integer('bak_control')->comment('Mengendalikan rangsangan BAK');
            $table->integer('eating')->comment('Makan minum');
            $table->integer('stairs')->comment('Naik turun tangga');
            $table->integer('bathing')->comment('Mandi');
            $table->integer('transfer')->comment('Bergerak dari kursi roda ke tempat tidur');
            $table->integer('walking')->comment('Berjalan di tempat rata');
            $table->integer('dressing')->comment('Berpakaian');
            $table->integer('grooming')->comment('Membersihkan diri');
            $table->integer('toilet_use')->comment('Penggunaan WC');
            
            $table->integer('total_score')->default(0)->comment('Total skor Barthel Index');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
