<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ketidakhadiran_harian', function (Blueprint $table) {
            $table->json('data_tambahan')->nullable()->after('keterangan');
        });
    }

    public function down()
    {
        Schema::table('ketidakhadiran_harian', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });
    }
};