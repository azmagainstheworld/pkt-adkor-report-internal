<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('masalah_kendala', function (Blueprint $table) {
            $table->json('data_tambahan')->nullable()->after('solusi');
        });
    }

    public function down()
    {
        Schema::table('masalah_kendala', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });
    }
};