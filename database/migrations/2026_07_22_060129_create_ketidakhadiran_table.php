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
        // Perbaikan: Kita gunakan Schema::create untuk MEMBUAT tabel baru 'ketidakhadiran'
        Schema::create('ketidakhadiran', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel karyawan. onDelete('cascade') artinya jika karyawan dihapus, data absennya ikut terhapus.
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->smallInteger('tahun');
            // Menggunakan enum untuk bulan agar data konsisten
            $table->enum('bulan', [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ]);
            // Kolom-kolom kategori absen bertipe integer untuk menyimpan jumlah hari, default 0.
            $table->integer('dinas')->default(0);
            $table->integer('cuti')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('training')->default(0);
            $table->integer('dispensasi')->default(0);
            $table->integer('detasering')->default(0);
            $table->timestamps();

            // Memastikan unik: 1 karyawan hanya punya 1 data rekap per bulan per tahun
            $table->unique(['karyawan_id', 'tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ketidakhadiran');
    }
};