<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mengubah kolom role agar mendukung nilai 'super_admin' selain 'admin' dan 'karyawan'.
     * Juga memperbarui kolom dari string biasa menjadi enum yang eksplisit.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ubah kolom role menjadi enum dengan tiga pilihan
            // Default tetap 'karyawan' untuk akun baru
            $table->enum('role', ['super_admin', 'admin', 'karyawan'])
                  ->default('karyawan')
                  ->change();
        });
    }

    /**
     * Rollback: kembalikan ke string dengan default 'karyawan'
     */
    public function down(): void
    {
        // Sebelum rollback, ubah data super_admin ke admin supaya tidak rusak
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('karyawan')->change();
        });
    }
};
