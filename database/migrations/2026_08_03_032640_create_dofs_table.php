<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Master Kolom DOF
        Schema::create('dof_masters', function (Blueprint $table) {
            $table->id();
            $table->integer('kelompok_tabel'); // 1 = Laporan DOF, 2 = Digital Signature
            $table->string('nama_kegiatan');
            $table->timestamps();
        });

        // Tabel Data Nilai DOF
        Schema::create('dof_data', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('bulan');
            $table->foreignId('master_id')->constrained('dof_masters')->onDelete('cascade');
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dof_data');
        Schema::dropIfExists('dof_masters');
    }
};