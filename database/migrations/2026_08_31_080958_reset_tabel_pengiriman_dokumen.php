<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. HANCURKAN 3 TABEL LAMA
        Schema::dropIfExists('pengiriman_costs');
        Schema::dropIfExists('pengiriman_volumes');
        Schema::dropIfExists('pengiriman_dokumen');

        // 2. BUAT 1 TABEL BARU YANG SEMPURNA
        Schema::create('pengiriman_dokumen', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('bulan', 50); 
            $table->integer('penerimaan_mailroom')->default(0);
            $table->integer('registrasi_surat_masuk_dof')->default(0);
            $table->integer('pengiriman_dalam_negeri')->default(0);
            $table->integer('pengiriman_luar_negeri')->default(0);
            $table->bigInteger('ongkir_dalam_negeri')->default(0);
            $table->bigInteger('ongkir_luar_negeri')->default(0);
            $table->integer('e_materai')->default(0);
            $table->json('data_tambahan')->nullable(); // Kolom sakti untuk kolom dinamis
            $table->timestamps();

            $table->unique(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman_dokumen');
    }
};