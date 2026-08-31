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
        Schema::table('perizinan_terbit', function (Blueprint $table) {
            $table->text('instansi_penerbit')->nullable()->change();
            $table->text('nomor')->nullable()->change();
        });
        
        Schema::table('jenis_perizinan_master', function (Blueprint $table) {
            $table->text('nama_jenis')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perizinan_terbit', function (Blueprint $table) {
            $table->string('instansi_penerbit', 150)->nullable()->change();
            $table->string('nomor', 100)->nullable()->change();
        });
        
        Schema::table('jenis_perizinan_master', function (Blueprint $table) {
            $table->string('nama_jenis', 255)->nullable()->change();
        });
    }
};
