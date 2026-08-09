<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Master (Untuk menyimpan nama-nama kolom)
        Schema::create('pa_teknik_masters', function (Blueprint $table) {
            $table->id();
            $table->integer('kelompok_tabel'); // 1 atau 2
            $table->string('nama_kegiatan');
            $table->timestamps();
        });

        // Tabel Data (Untuk menyimpan nilai/jumlahnya)
        Schema::create('pa_teknik_data', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('bulan');
            $table->foreignId('master_id')->constrained('pa_teknik_masters')->onDelete('cascade');
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pa_teknik_data');
        Schema::dropIfExists('pa_teknik_masters');
    }
};