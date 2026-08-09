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
        Schema::create('pa_teknik', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->enum('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
            $table->integer('alih_media_berkas_lembar');
            $table->integer('jasa_cetak_gambar_berkas');
            $table->integer('peminjaman_dok_berkas_lembar');
            $table->integer('peminjaman_dok_bantex');
            $table->integer('peminjaman_dok_cd');
            $table->integer('peminjaman_dok_lembar');
            $table->integer('permintaan_copy_soft_berkas_lembar');
            $table->integer('konversi_tif_ke_pdf_file');
            $table->timestamps();
            $table->unique(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pa_teknik');
    }
};
