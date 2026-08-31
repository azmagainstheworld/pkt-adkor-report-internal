<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_strategis', function (Blueprint $table) {
            // Cek dan tambah kolom baru dengan aman (tanpa enum yang bikin ribet)
            if (!Schema::hasColumn('program_strategis', 'bulan')) {
                $table->string('bulan', 2)->nullable();
            }
            if (!Schema::hasColumn('program_strategis', 'sasaran')) {
                $table->string('sasaran')->nullable();
            }
            if (!Schema::hasColumn('program_strategis', 'target_waktu_start')) {
                $table->date('target_waktu_start')->nullable();
            }
            if (!Schema::hasColumn('program_strategis', 'target_waktu_end')) {
                $table->date('target_waktu_end')->nullable();
            }
            if (!Schema::hasColumn('program_strategis', 'kendala')) {
                $table->text('kendala')->nullable();
            }
            if (!Schema::hasColumn('program_strategis', 'status')) {
                $table->string('status')->default('In Progress');
            }
        });
    }

    public function down(): void
    {
        Schema::table('program_strategis', function (Blueprint $table) {
            $table->dropColumn(['bulan', 'sasaran', 'target_waktu_start', 'target_waktu_end', 'kendala', 'status']);
        });
    }
};