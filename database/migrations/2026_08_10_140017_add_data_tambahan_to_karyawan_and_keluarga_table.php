<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->json('data_tambahan')->nullable()->after('status');
        });

        Schema::table('keluarga_karyawan', function (Blueprint $table) {
            $table->json('data_tambahan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });

        Schema::table('keluarga_karyawan', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });
    }
};