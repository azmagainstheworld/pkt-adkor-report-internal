<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surat', function (Blueprint $table) {
            // Menambahkan kolom data_tambahan jika belum ada
            if (!Schema::hasColumn('surat', 'data_tambahan')) {
                $table->json('data_tambahan')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('surat', function (Blueprint $table) {
            if (Schema::hasColumn('surat', 'data_tambahan')) {
                $table->dropColumn('data_tambahan');
            }
        });
    }
};