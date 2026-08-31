<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pa_tekstual_master', function (Blueprint $table) {
            // Kita cek dulu, kalau kolomnya belum ada, baru kita tambahin
            if (!Schema::hasColumn('pa_tekstual_master', 'kelompok_tabel')) {
                // Tambahin kolom kelompok_tabel biar controller nggak bingung lagi!
                $table->tinyInteger('kelompok_tabel')->default(1)->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pa_tekstual_master', function (Blueprint $table) {
            if (Schema::hasColumn('pa_tekstual_master', 'kelompok_tabel')) {
                $table->dropColumn('kelompok_tabel');
            }
        });
    }
};