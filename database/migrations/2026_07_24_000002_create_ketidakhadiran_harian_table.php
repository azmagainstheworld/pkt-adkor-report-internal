<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel ini menyimpan catatan ketidakhadiran PER HARI, per karyawan.
     * Setiap baris = 1 karyawan, 1 tanggal, 1 jenis ketidakhadiran
     * (maksimal 1 jenis per hari per karyawan, sesuai unique constraint di bawah).
     *
     * Setiap kali ada baris baru di sini, controller akan otomatis menambah +1
     * ke kolom kategori terkait di tabel 'ketidakhadiran' (bulanan) untuk
     * karyawan + bulan + tahun yang sesuai. Tabel ini sendiri hanya berfungsi
     * sebagai catatan detail harian, bukan sumber utama rekap bulanan.
     */
    public function up(): void
    {
        Schema::create('ketidakhadiran_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('jenis', ['dinas', 'cuti', 'izin', 'training', 'dispensasi', 'detasering']);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Maksimal 1 jenis ketidakhadiran per karyawan per tanggal
            $table->unique(['karyawan_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ketidakhadiran_harian');
    }
};