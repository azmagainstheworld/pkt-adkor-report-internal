<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dof_data', function (Blueprint $table) {
            if (!Schema::hasColumn('dof_data', 'data_tambahan')) {
                $table->json('data_tambahan')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('dof_data', function (Blueprint $table) {
            if (Schema::hasColumn('dof_data', 'data_tambahan')) {
                $table->dropColumn('data_tambahan');
            }
        });
    }
};