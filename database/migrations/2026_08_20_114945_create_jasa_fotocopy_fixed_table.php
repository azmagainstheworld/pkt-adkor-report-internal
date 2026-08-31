<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('jasa_fotocopy_data');
        Schema::dropIfExists('jasa_fotocopy_masters');
        Schema::dropIfExists('jasa_fotocopy');

        Schema::create('jasa_fotocopy', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->string('bulan', 20);
            $table->string('unit_kerja', 150);
            $table->string('cost_centre', 100)->nullable();
            $table->string('keterangan', 100)->nullable()->default('KOPKAR');
            $table->string('tipe_mesin', 100)->nullable();
            $table->integer('pemakaian_lembar')->default(0);
            $table->decimal('biaya_fee_per_lembar', 10, 2)->default(47.22);
            $table->decimal('biaya_sewa_mesin', 15, 2)->default(909000);
            $table->json('data_tambahan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jasa_fotocopy');
    }
};