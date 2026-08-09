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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('npk', 30)->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_ptk')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('foto')->nullable();
            
            $table->enum('gol_grade', [
                'I-A', 'I-B', 'I-C', 'I-D', 
                'II-A', 'II-B', 'II-C', 'II-D', 
                'III-A', 'III-B', 'III-C', 'III-D', 
                'IV-A', 'IV-B', 'IV-C', 'IV-D'
            ]);
            
            $table->date('mpp_pbp');
            $table->enum('keterangan', ['Organik', 'Non Organik']);
            $table->enum('ket_pensiun', ['> 10 Tahun', '< 10 Tahun', '< 5 Tahun'])->nullable();
            $table->text('alamat');
            $table->enum('ukuran_kaos', ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL']);
            
            // --- HAPUS BARIS INI ---
            // $table->enum('ukuran_kaos_tipe', ['3/4', 'Lengan Panjang']); 
            // -----------------------
            
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};