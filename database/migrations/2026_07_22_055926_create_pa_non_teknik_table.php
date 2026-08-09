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
        Schema::create('pa_non_teknik', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->enum('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
            $table->integer('permintaan_dok_asli_berkas');
            $table->integer('penyerahan_dok_asli_berkas');
            $table->integer('penyerahan_dok_asli_bantex');
            $table->integer('peminjaman_dok_asli_berkas');
            $table->integer('permintaan_copy_softcopy_berkas');
            $table->integer('alih_media_lembar');
            $table->integer('rekaman_rapat');
            $table->integer('upload_dokumen_paradm');
            $table->integer('penyerahan_dokumen_inaktif');
            $table->integer('pengecekan_dokumen_inaktif_box');
            $table->integer('pemusnahan_dokumen_berkas');
            $table->integer('stock_name_dokumen_vital');
            $table->integer('permintaan_box_arsip_pcs');
            $table->timestamps();
            $table->unique(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pa_non_teknik');
    }
};
