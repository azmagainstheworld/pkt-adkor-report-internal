<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perizinan_terbit', function (Blueprint $table) {
            // Menambahkan kolom JSON untuk menampung kolom dinamis
            $table->json('data_tambahan')->nullable()->after('instansi_penerbit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perizinan_terbit', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });
    }
};