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
        // SOLUSI UNBLOCK: Cek dulu apakah tabelnya ada.
        // Jika tidak ada, Laravel akan melewati proses ini tanpa ERROR.
        if (Schema::hasTable('pa_tekstual_data')) {
            Schema::table('pa_tekstual_data', function (Blueprint $table) {
                // Tambahan pengecekan kolom agar benar-benar aman
                if (!Schema::hasColumn('pa_tekstual_data', 'data_tambahan')) {
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
        if (Schema::hasTable('pa_tekstual_data')) {
            Schema::table('pa_tekstual_data', function (Blueprint $table) {
                if (Schema::hasColumn('pa_tekstual_data', 'data_tambahan')) {
                    $table->dropColumn('data_tambahan');
                }
            });
        }
    }
};