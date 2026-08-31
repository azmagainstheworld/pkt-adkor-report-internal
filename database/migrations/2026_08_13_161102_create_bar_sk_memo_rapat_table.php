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
        Schema::create('bar_sk_memo_rapat', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_rapat')->comment('Tanggal pelaksanaan rapat');
            $table->smallInteger('tahun')->comment('Tahun pelaksanaan rapat (digunakan untuk filter)');
            $table->string('tentang')->comment('Perihal atau agenda rapat');
            $table->string('status')->comment('Status rapat (misal: Selesai, Dalam Proses)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bar_sk_memo_rapat');
    }
};