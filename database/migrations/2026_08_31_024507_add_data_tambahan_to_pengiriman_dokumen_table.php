<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengiriman_dokumen', function (Blueprint $table) {
            $table->json('data_tambahan')->nullable()->after('e_materai');
        });
    }

    public function down(): void
    {
        Schema::table('pengiriman_dokumen', function (Blueprint $table) {
            $table->dropColumn('data_tambahan');
        });
    }
};