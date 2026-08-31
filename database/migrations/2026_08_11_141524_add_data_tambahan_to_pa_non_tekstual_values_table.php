<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pa_non_tekstual_values', function (Blueprint $table) {
            // Pengaman agar tidak error jika kolom sudah ada
            if (!Schema::hasColumn('pa_non_tekstual_values', 'data_tambahan')) {
                $table->json('data_tambahan')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('pa_non_tekstual_values', function (Blueprint $table) {
            if (Schema::hasColumn('pa_non_tekstual_values', 'data_tambahan')) {
                $table->dropColumn('data_tambahan');
            }
        });
    }
};