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
        Schema::create('pelaporan', function (Blueprint $table) {
            $table->id();
            $table->enum('tujuan', ['Eksternal', 'Internal']);
            $table->string('nomor', 100);
            $table->string('laporan', 255);
            $table->date('tanggal');
            $table->enum('jenis', ['Bulan', 'Semester', 'Triwulan', 'Tahun']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaporan');
    }
};
