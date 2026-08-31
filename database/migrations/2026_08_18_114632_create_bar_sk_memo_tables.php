<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Dokumen
        if (!Schema::hasTable('bar_sk_memo_master')) {
            Schema::create('bar_sk_memo_master', function (Blueprint $table) {
                $table->id();
                $table->string('tipe')->nullable(); 
                $table->string('nama_dokumen')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tabel Data Jumlah Dokumen per Bulan (Ini yang tadi bikin Error 500)
        if (!Schema::hasTable('bar_sk_memo_data')) {
            Schema::create('bar_sk_memo_data', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dokumen_id')->nullable();
                $table->integer('tahun')->nullable();
                $table->string('bulan')->nullable();
                $table->integer('jumlah')->default(0);
                $table->timestamps();
            });
        }

        // 3. Tabel Data Rapat
        if (!Schema::hasTable('bar_sk_memo_rapat')) {
            Schema::create('bar_sk_memo_rapat', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun')->nullable();
                $table->date('tanggal_rapat')->nullable();
                $table->text('tentang')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        // 4. Tabel SK Direksi Terbit
        if (!Schema::hasTable('skd_terbits')) {
            Schema::create('skd_terbits', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun')->nullable();
                $table->string('nomor_skd')->nullable();
                $table->text('tentang')->nullable();
                $table->date('tanggal_penetapan')->nullable();
                $table->date('tanggal_salinan')->nullable();
                $table->string('drafter')->nullable();
                $table->string('kategori')->nullable();
                $table->timestamps();
            });
        }

        // 5. Tabel SK Direksi Proses
        if (!Schema::hasTable('skd_proses')) {
            Schema::create('skd_proses', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun')->nullable();
                $table->date('tanggal_permintaan')->nullable();
                $table->text('tentang')->nullable();
                $table->string('unit_kerja_peminta')->nullable();
                $table->string('kategori')->nullable();
                $table->timestamps();
            });
        }

        // 6. Tabel Memo Terbit
        if (!Schema::hasTable('memo_terbits')) {
            Schema::create('memo_terbits', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun')->nullable();
                $table->string('nomor_memo')->nullable();
                $table->text('tentang')->nullable();
                $table->date('tanggal')->nullable();
                $table->timestamps();
            });
        }

        // 7. Tabel Memo Proses
        if (!Schema::hasTable('memo_proses')) {
            Schema::create('memo_proses', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun')->nullable();
                $table->date('tanggal_permintaan')->nullable();
                $table->text('tentang')->nullable();
                $table->string('unit_kerja_peminta')->nullable();
                $table->timestamps();
            });
        }
        
        // 8. Tabel Ekstra
        if (!Schema::hasTable('bar_sk_memo_bulanan')) {
            Schema::create('bar_sk_memo_bulanan', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun')->nullable();
                $table->string('bulan')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bar_sk_memo_bulanan');
        Schema::dropIfExists('memo_proses');
        Schema::dropIfExists('memo_terbits');
        Schema::dropIfExists('skd_proses');
        Schema::dropIfExists('skd_terbits');
        Schema::dropIfExists('bar_sk_memo_rapat');
        Schema::dropIfExists('bar_sk_memo_data');
        Schema::dropIfExists('bar_sk_memo_master');
    }
};