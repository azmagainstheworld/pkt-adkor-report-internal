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
        Schema::create('pengiriman_dokumen', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->enum('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
            $table->integer('penerimaan_mailroom');
            $table->integer('registrasi_surat_masuk_dof');
            $table->integer('pengiriman_dalam_negeri');
            $table->integer('pengiriman_luar_negeri');
            $table->decimal('ongkir_dalam_negeri', 15, 2);
            $table->decimal('ongkir_luar_negeri', 15, 2);
            $table->integer('e_materai');
            $table->timestamps();
            $table->unique(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_dokumen');
    }
};
