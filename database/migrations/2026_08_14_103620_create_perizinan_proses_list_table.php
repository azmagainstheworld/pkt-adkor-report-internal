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
        Schema::create('perizinan_proses_list', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('nama_proses', 255);
            $table->text('target')->nullable();
            $table->string('periode', 100)->nullable();
            $table->json('data_tambahan')->nullable(); // Untuk menampung kolom dinamis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan_proses_list');
    }
};