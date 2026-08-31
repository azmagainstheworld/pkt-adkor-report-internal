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
        // PENTING: User diminta truncate data surat sebelum menjalankan ini.

        Schema::table('surat', function (Blueprint $table) {
            // 1. Hapus Unique Index Lama (tahun, bulan)
            // Gunakan dropUniqueIfExists jika tersedia, atau wrap dalam try-catch jika driver DB mendukung rollback DDL
            // Cara paling aman: cek index lewat DB builder (agak rumit),
            // atau asumsikan index ada karena ini tabel lama.
            try {
                $table->dropUnique('surat_tahun_bulan_unique');
            } catch (\Exception $e) {
                // Abaikan jika index sudah dihapus sebelumnya
            }

            // 2. Hapus Kolom Akumulasi Lama
            // Kita gunakan check sebelum drop agar tidak error jika kolom sudah dihapus parsial sebelumnya
            $columnsToDrop = [];
            if (Schema::hasColumn('surat', 'surat_masuk')) {
                $columnsToDrop[] = 'surat_masuk';
            }
            if (Schema::hasColumn('surat', 'surat_keluar')) {
                $columnsToDrop[] = 'surat_keluar';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            // 3. Ubah tipe data tahun/bulan agar konsisten dengan data transaksional
            $table->integer('tahun')->change();
            $table->string('bulan')->change();
        });

        // 4. Tambahkan Kolom Baru untuk Data Satuan (Req 1) secara kondisional
        // Kita pisah Schema::table agar pengecekan hasColumn akurat setelah dropColumn diatas selesai
        Schema::table('surat', function (Blueprint $table) {
            if (!Schema::hasColumn('surat', 'nomor_surat')) {
                $table->string('nomor_surat')->after('bulan'); // Wajib isi
            }
            if (!Schema::hasColumn('surat', 'tanggal_surat')) {
                $table->date('tanggal_surat')->after('nomor_surat'); // Wajib isi
            }
            if (!Schema::hasColumn('surat', 'drafter')) {
                $table->string('drafter')->nullable()->after('tanggal_surat'); // Nullable
            }
            if (!Schema::hasColumn('surat', 'judul_surat')) {
                $table->string('judul_surat')->after('drafter'); // Wajib isi
            }
            if (!Schema::hasColumn('surat', 'file_path')) {
                $table->string('file_path')->nullable()->after('judul_surat'); // Nullable
            }
            if (!Schema::hasColumn('surat', 'status')) {
                $table->enum('status', ['Terkirim', 'Dibatalkan'])->default('Terkirim')->after('file_path');
            }
            if (!Schema::hasColumn('surat', 'jenis_surat')) {
                $table->enum('jenis_surat', ['Surat Masuk', 'Surat Keluar'])->after('status');
            }

            // INI PERBAIKANNYA: Cek dulu sebelum add 'data_tambahan'
            if (!Schema::hasColumn('surat', 'data_tambahan')) {
                $table->json('data_tambahan')->nullable()->after('jenis_surat'); // Kolom Dinamis
            }

            // 5. Tambahkan Unique Index untuk Nomor Surat (kondisional)
            // Laravel tidak memiliki hasUniqueIndex bawaan yang mudah.
            // Kita asumsikan jika nomor_surat baru dibuat, index juga belum ada.
            // Namun untuk amannya, kita wrap dalam try-catch.
            try {
                $table->unique('nomor_surat');
            } catch (\Exception $e) {
                // Abaikan jika unique key sudah ada
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // PENTING: Truncate data satuan sebelum rollback

        Schema::table('surat', function (Blueprint $table) {
            // Hapus unique satuan (kondisional)
            try {
                $table->dropUnique('surat_nomor_surat_unique');
            } catch (\Exception $e) {}

            // Hapus kolom satuan (kondisional)
            $newColumns = ['nomor_surat', 'tanggal_surat', 'drafter', 'judul_surat', 'file_path', 'status', 'jenis_surat', 'data_tambahan'];
            $columnsToDrop = [];
            foreach ($newColumns as $column) {
                if (Schema::hasColumn('surat', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            // Kembalikan tipe data (Ubah kembali ke enum memerlukan DDL spesifik, kadang tidak jalan di change())
            // Cara paling aman rollback change enum adalah menimpa definisinya
            $table->smallInteger('tahun')->change();
            $table->enum('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'])->change();

            // Tambah kolom akumulasi lama (kondisional)
            if (!Schema::hasColumn('surat', 'surat_masuk')) {
                $table->integer('surat_masuk')->after('bulan');
            }
            if (!Schema::hasColumn('surat', 'surat_keluar')) {
                $table->integer('surat_keluar')->after('surat_masuk');
            }

            // Tambah unique lama (kondisional)
            try {
                $table->unique(['tahun', 'bulan']);
            } catch (\Exception $e) {}
        });
    }
};