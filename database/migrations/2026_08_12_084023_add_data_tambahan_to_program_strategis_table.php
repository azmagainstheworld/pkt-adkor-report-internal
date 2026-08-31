<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('program_strategis', function (Blueprint $table) {
            if (!Schema::hasColumn('program_strategis', 'data_tambahan')) {
                $table->json('data_tambahan')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('program_strategis', function (Blueprint $table) {
            if (Schema::hasColumn('program_strategis', 'data_tambahan')) {
                $table->dropColumn('data_tambahan');
            }
        });
    }
};