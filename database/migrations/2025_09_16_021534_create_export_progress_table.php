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
        Schema::create('export_progress', function (Blueprint $table) {
            $table->id();
            $table->string('export_id')->unique();
            $table->string('user_id'); // String to match users table UUID
            $table->string('type')->default('pasien');
            $table->integer('percentage')->default(0);
            $table->text('message');
            $table->enum('status', ['processing', 'success', 'error', 'warning'])->default('processing');
            $table->json('data')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_progress');
    }
};
