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
            // Make all skrining fields nullable
            $table->integer('bab_control')->nullable()->change();
            $table->integer('bak_control')->nullable()->change();
            $table->integer('eating')->nullable()->change();
            $table->integer('stairs')->nullable()->change();
            $table->integer('bathing')->nullable()->change();
            $table->integer('transfer')->nullable()->change();
            $table->integer('walking')->nullable()->change();
            $table->integer('dressing')->nullable()->change();
            $table->integer('grooming')->nullable()->change();
            $table->integer('toilet_use')->nullable()->change();
            $table->integer('total_score')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skrining_adl', function (Blueprint $table) {
            // Revert fields back to not nullable
            $table->integer('bab_control')->nullable(false)->change();
            $table->integer('bak_control')->nullable(false)->change();
            $table->integer('eating')->nullable(false)->change();
            $table->integer('stairs')->nullable(false)->change();
            $table->integer('bathing')->nullable(false)->change();
            $table->integer('transfer')->nullable(false)->change();
            $table->integer('walking')->nullable(false)->change();
            $table->integer('dressing')->nullable(false)->change();
            $table->integer('grooming')->nullable(false)->change();
            $table->integer('toilet_use')->nullable(false)->change();
            $table->integer('total_score')->nullable(false)->change();
        });
    }
};
