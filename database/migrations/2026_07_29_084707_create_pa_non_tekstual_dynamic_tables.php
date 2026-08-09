<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Jenis Dokumen / Variabel (Dinamis)
        Schema::create('pa_non_tekstual_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Contoh: 'Alih Media', 'Rekap Sidovit', 'Variabel Baru'
            $table->boolean('is_active')->default(true); // Untuk hapus logis
            $table->timestamps();
        });

        // 2. Tabel Data Nilai (Menghubungkan Periode, Jenis, dan Jumlah)
        Schema::create('pa_non_tekstual_values', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->string('bulan');
            
            // Relasi ke tabel master jenis
            $table->foreignId('type_id')->constrained('pa_non_tekstual_types')->onDelete('cascade');
            
            $table->integer('jumlah')->default(0);
            $table->timestamps();

            // Unique constraint agar tidak ada duplikasi jenis di bulan/tahun yang sama
            $table->unique(['tahun', 'bulan', 'type_id'], 'pa_nt_value_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pa_non_tekstual_values');
        Schema::dropIfExists('pa_non_tekstual_types');
    }
};