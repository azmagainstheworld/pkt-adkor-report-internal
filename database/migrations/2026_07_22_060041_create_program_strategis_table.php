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
        Schema::create('program_strategis', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->string('program', 255);
            $table->string('target', 255);
            $table->string('realisasi', 255);
            $table->enum('status', ['Tercapai', 'Berjalan', 'Tertunda']);
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_strategis');
    }
};
