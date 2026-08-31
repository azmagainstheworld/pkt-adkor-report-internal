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
        // 1. Perbaikan tabel pemeliharaan_rutin
        // Nama tabel 'pemeliharaan_rutin' sudah benar.
        if (Schema::hasTable('pemeliharaan_rutin')) {
            Schema::table('pemeliharaan_rutin', function (Blueprint $table) {
                // Cek apakah kolom sudah ada sebelum menambahkan
                if (!Schema::hasColumn('pemeliharaan_rutin', 'data_tambahan')) {
                    $table->json('data_tambahan')->nullable();
                }
            });
        }
        
        // 2. Perbaikan tabel pemeliharaan_peralatan_data
        // Diubah dari 'pemeliharaan_peralatan' menjadi 'pemeliharaan_peralatan_data'
        if (Schema::hasTable('pemeliharaan_peralatan_data')) {
            Schema::table('pemeliharaan_peralatan_data', function (Blueprint $table) {
                // Cek apakah kolom sudah ada sebelum menambahkan
                if (!Schema::hasColumn('pemeliharaan_peralatan_data', 'data_tambahan')) {
                    $table->json('data_tambahan')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan perubahan dengan nama tabel yang benar

        if (Schema::hasTable('pemeliharaan_rutin')) {
            Schema::table('pemeliharaan_rutin', function (Blueprint $table) {
                if (Schema::hasColumn('pemeliharaan_rutin', 'data_tambahan')) {
                    $table->dropColumn('data_tambahan');
                }
            });
        }
        
        if (Schema::hasTable('pemeliharaan_peralatan_data')) {
            Schema::table('pemeliharaan_peralatan_data', function (Blueprint $table) {
                if (Schema::hasColumn('pemeliharaan_peralatan_data', 'data_tambahan')) {
                    $table->dropColumn('data_tambahan');
                }
            });
        }
    }
};