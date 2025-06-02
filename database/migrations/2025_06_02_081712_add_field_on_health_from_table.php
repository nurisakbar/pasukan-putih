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
            $table->boolean('non_medical_issues_status')->default(false);
            $table->string('non_medical_issues_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_forms', function (Blueprint $table) {
            $table->dropColumn('non_medical_issues_status');
            $table->dropColumn('non_medical_issues_text');
        });
    }
};
