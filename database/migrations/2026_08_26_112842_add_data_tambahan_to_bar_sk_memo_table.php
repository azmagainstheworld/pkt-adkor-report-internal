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
        Schema::table('bar_sk_memo', function (Blueprint $table) {
            // Tambahkan 2 kolom ini setelah kolom proses_bar_manajemen
            if (!Schema::hasColumn('bar_sk_memo', 'data_tambahan')) {
                $table->json('data_tambahan')->nullable()->after('proses_bar_manajemen');
            }
            if (!Schema::hasColumn('bar_sk_memo', 'data_tambahan_proses')) {
                $table->json('data_tambahan_proses')->nullable()->after('data_tambahan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bar_sk_memo', function (Blueprint $table) {
            // Hapus kolomnya kalau di-rollback
            if (Schema::hasColumn('bar_sk_memo', 'data_tambahan')) {
                $table->dropColumn('data_tambahan');
            }
            if (Schema::hasColumn('bar_sk_memo', 'data_tambahan_proses')) {
                $table->dropColumn('data_tambahan_proses');
            }
        });
    }
};