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
        // Schema::table('users', function (Blueprint $table) {
        //     $table->string('status_pegawai')->nullable();
        //     $table->string('village')->nullable();
        //     $table->string('district')->nullable();
        //     $table->string('regency')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status_pegawai');
            $table->dropColumn('village');
            $table->dropColumn('district');
            $table->dropColumn('regency');
        });
    }
};
