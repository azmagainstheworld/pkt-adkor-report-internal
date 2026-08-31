<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('anggaran_administrasi', function (Blueprint $table) {
            $table->json('data_tambahan')->nullable()->after('keterangan');
        });
    }

    public function down()
    {
        Schema::table('anggaran_administrasi', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });
    }
};