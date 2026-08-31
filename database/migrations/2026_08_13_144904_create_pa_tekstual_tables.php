<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration ini sebelumnya HILANG dari urutan — file
     * 2026_08_11_133152_add_data_tambahan_to_pa_tekstual_data_table sudah ada
     * dan mencoba ALTER TABLE pa_tekstual_data, tapi tidak pernah ada migration
     * yang benar-benar men-CREATE tabelnya. Migration ini mengisi bagian yang
     * hilang itu, mengikuti struktur PERSIS sama seperti tabel kembarnya
     * (pa_teknik_master / pa_teknik_data) yang sudah ada.
     */
    public function up(): void
    {
        if (!Schema::hasTable('pa_tekstual_master')) {
            Schema::create('pa_tekstual_master', function (Blueprint $table) {
                $table->id();
                $table->string('nama_dokumen', 150);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pa_tekstual_data')) {
            Schema::create('pa_tekstual_data', function (Blueprint $table) {
                $table->id();
                $table->foreignId('master_id')->constrained('pa_tekstual_master')->onDelete('cascade');
                $table->integer('tahun');
                $table->string('bulan', 20);
                $table->integer('jumlah')->default(0);
                $table->timestamps();
                $table->unique(['master_id', 'tahun', 'bulan']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pa_tekstual_data');
        Schema::dropIfExists('pa_tekstual_master');
    }
};