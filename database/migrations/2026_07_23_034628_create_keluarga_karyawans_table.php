<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluarga_karyawan', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key yang menghubungkan ke tabel karyawan
            // onDelete('cascade') berarti jika data karyawan dihapus, data keluarganya ikut terhapus
            $table->foreignId('karyawan_id')
                  ->constrained('karyawan')
                  ->onDelete('cascade');
                  
            $table->string('nama', 150);
            $table->enum('hubungan', ['Suami', 'Istri', 'Anak']);
            
            // Kolom ini sudah disesuaikan dengan yang ada di phpMyAdmin dan UI web
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluarga_karyawan');
    }
};