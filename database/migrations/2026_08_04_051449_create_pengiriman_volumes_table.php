<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman_volumes', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('bulan'); // Kita simpan teks bulan sesuai Excel (Januari, Februari)
            $table->integer('volume_mailroom')->default(0);
            $table->integer('volume_domestik')->default(0);
            $table->integer('volume_internasional')->default(0);
            $table->integer('volume_dof')->default(0); // Kolom F di Excel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman_volumes');
    }
};