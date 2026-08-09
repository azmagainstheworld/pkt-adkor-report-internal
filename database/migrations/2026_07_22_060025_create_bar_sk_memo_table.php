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
        Schema::create('bar_sk_memo', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->enum('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
            $table->integer('ba_terbit');
            $table->integer('ba_proses');
            $table->integer('skd_keputusan_bersama_terbit');
            $table->integer('skd_non_ratifikasi_terbit');
            $table->integer('skd_ratifikasi_terbit');
            $table->integer('memo_direksi_terbit');
            $table->integer('bar_monitoring_terbit');
            $table->integer('bar_manajemen_terbit');
            $table->integer('proses_skd_keputusan_bersama');
            $table->integer('proses_skd_non_ratifikasi');
            $table->integer('proses_skd_ratifikasi');
            $table->integer('proses_memo_direksi');
            $table->integer('proses_bar_monitoring');
            $table->integer('proses_bar_manajemen');
            $table->timestamps();
            $table->unique(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bar_sk_memo');
    }
};
