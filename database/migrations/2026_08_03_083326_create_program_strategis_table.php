<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('program_strategis');
        
        Schema::create('program_strategis', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->text('program_strategis'); // Teks nama program
            $table->text('deskripsi_kegiatan')->nullable(); // Bisa teks panjang
            $table->string('target_waktu')->nullable(); // Misal: "Januari - April 2026"
            $table->string('realisasi')->nullable(); // Misal: "93%"
            $table->longText('progress_saat_ini')->nullable(); // Teks sangat panjang dengan baris baru
            $table->string('keterangan_tambahan')->nullable(); // Untuk mengakomodasi 'Column 1' di gambar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_strategis');
    }
};