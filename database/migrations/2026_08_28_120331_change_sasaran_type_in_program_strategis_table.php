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
        Schema::table('program_strategis', function (Blueprint $table) {
            $table->text('sasaran')->nullable()->change();
            $table->text('program_strategis')->nullable()->change();
            $table->text('deskripsi_kegiatan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_strategis', function (Blueprint $table) {
            $table->string('sasaran')->nullable()->change();
            $table->string('program_strategis')->nullable()->change();
            $table->text('deskripsi_kegiatan')->nullable()->change();
        });
    }
};
