<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel lama jika ada
        Schema::dropIfExists('jasa_fotocopy');

        // 1. Tabel Master (Data Mesin & Unit Kerja)
        Schema::create('jasa_fotocopy_masters', function (Blueprint $table) {
            $table->id();
            $table->string('unit_kerja', 150);
            $table->string('cost_centre', 100)->nullable();
            $table->string('keterangan', 100)->nullable()->default('KOPKAR');
            $table->string('tipe_mesin', 100)->nullable();
            $table->timestamps();
        });

        // 2. Tabel Data Transaksi (Diinput per bulan)
        Schema::create('jasa_fotocopy_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained('jasa_fotocopy_masters')->onDelete('cascade');
            $table->smallInteger('tahun');
            $table->string('bulan', 20);
            $table->integer('pemakaian_lembar')->default(0);
            $table->decimal('biaya_fee_per_lembar', 10, 2)->default(47.22);
            $table->decimal('biaya_sewa_mesin', 15, 2)->default(909000);
            $table->json('data_tambahan')->nullable();
            $table->timestamps();
            $table->unique(['master_id', 'tahun', 'bulan']); // Pencegah duplikat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jasa_fotocopy_data');
        Schema::dropIfExists('jasa_fotocopy_masters');
    }
};