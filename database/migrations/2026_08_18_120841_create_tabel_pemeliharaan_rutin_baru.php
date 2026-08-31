<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. BIKIN TABEL MASTER RUTIN
        if (!Schema::hasTable('pemeliharaan_rutin_master')) {
            Schema::create('pemeliharaan_rutin_master', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pemeliharaan', 150);
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });

            // Langsung otomatis kita isikan data master bawaannya biar gampang!
            DB::table('pemeliharaan_rutin_master')->insert([
                ['nama_pemeliharaan' => 'Pemeliharaan Furnitur', 'created_at' => now(), 'updated_at' => now()],
                ['nama_pemeliharaan' => 'Penyiapan Furnitur', 'created_at' => now(), 'updated_at' => now()],
                ['nama_pemeliharaan' => 'Penyiapan Peralatan', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 2. BIKIN TABEL DATA RUTIN
        if (!Schema::hasTable('pemeliharaan_rutin_data')) {
            Schema::create('pemeliharaan_rutin_data', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rutin_id')->constrained('pemeliharaan_rutin_master')->onDelete('cascade');
                $table->smallInteger('tahun');
                $table->string('bulan', 20);
                $table->integer('jumlah')->default(0);
                $table->json('data_tambahan')->nullable();
                $table->timestamps();
                $table->unique(['rutin_id', 'tahun', 'bulan']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeliharaan_rutin_data');
        Schema::dropIfExists('pemeliharaan_rutin_master');
    }
};