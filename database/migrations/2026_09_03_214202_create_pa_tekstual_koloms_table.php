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
        Schema::create('pa_tekstual_kolom', function (Blueprint $table) {
            $table->id();
            $table->integer('kelompok_tabel')->default(1);
            $table->string('nama_kolom');
            $table->enum('tipe_input', ['text', 'number', 'currency'])->default('text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pa_tekstual_kolom');
    }
};
