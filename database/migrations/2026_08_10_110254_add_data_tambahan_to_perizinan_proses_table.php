<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/2026_08_10_110254_add_data_tambahan_to_perizinan_proses_table.php

    public function up(): void
    {
        Schema::table('perizinan_proses', function (Blueprint $table) {
            // Hapus ->after('periode') karena kolom 'periode' tidak ada
            $table->json('data_tambahan')->nullable(); 
        });
    }

    public function down(): void
    {
        Schema::table('perizinan_proses', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });
    }
};