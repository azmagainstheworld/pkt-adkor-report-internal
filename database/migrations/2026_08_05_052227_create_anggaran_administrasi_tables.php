<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnggaranAdministrasiTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anggaran_administrasi', function (Blueprint $create) {
            $create->id();
            $create->year('tahun');
            $create->string('bulan');
            $create->string('kategori'); // Dikelola / Rutin / Investasi
            $create->string('detail_anggaran'); // misal Biaya Pemeliharaan IT, Konsumsi Rapat, dst.

            // Kolom Anggaran Mentah (Gunakan BigInteger untuk mata uang tanpa desimal)
            $create->bigInteger('rkap');
            $create->bigInteger('komitmen');
            $create->bigInteger('realisasi');

            $create->text('keterangan')->nullable();

            $create->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anggaran_administrasi');
    }
}