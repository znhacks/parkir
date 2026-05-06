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
        Schema::create('parkir_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parkir_id')->constrained('parkir')->onDelete('cascade');
            $table->enum('aksi', ['masuk', 'keluar', 'edit']);
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkir_history');
    }
};
