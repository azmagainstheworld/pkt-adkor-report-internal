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
        Schema::create('perizinan_terbit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_perizinan_id')->constrained('jenis_perizinan_master')->onDelete('cascade');
            $table->string('nomor', 100);
            $table->date('tanggal_sejak');
            $table->date('tanggal_akhir');
            $table->string('instansi_penerbit', 150);
            $table->string('kegiatan', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan_terbit');
    }
};
