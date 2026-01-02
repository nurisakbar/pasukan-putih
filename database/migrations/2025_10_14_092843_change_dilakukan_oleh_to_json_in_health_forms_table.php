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
        Schema::table('health_forms', function (Blueprint $table) {
            $table->json('dilakukan_oleh')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_forms', function (Blueprint $table) {
            $table->string('dilakukan_oleh')->nullable()->change();
        });
    }
};
