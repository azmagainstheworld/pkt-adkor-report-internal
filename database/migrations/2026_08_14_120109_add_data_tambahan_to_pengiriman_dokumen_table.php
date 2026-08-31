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
        Schema::table('pengiriman_dokumen', function (Blueprint $table) {
            // Menambahkan kolom JSON untuk menampung kolom dinamis
            $table->json('data_tambahan')->nullable()->after('e_materai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengiriman_dokumen', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });
    }
};