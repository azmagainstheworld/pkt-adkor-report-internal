<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ketidakhadiran', function (Blueprint $table) {
            $table->string('dinas', 255)->default('0')->change();
            $table->string('cuti', 255)->default('0')->change();
            $table->string('izin', 255)->default('0')->change();
            $table->string('training', 255)->default('0')->change();
            $table->string('dispensasi', 255)->default('0')->change();
            $table->string('detasering', 255)->default('0')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ketidakhadiran', function (Blueprint $table) {
            // Reverting back might fail if there's text, but we try
            $table->integer('dinas')->default(0)->change();
            $table->integer('cuti')->default(0)->change();
            $table->integer('izin')->default(0)->change();
            $table->integer('training')->default(0)->change();
            $table->integer('dispensasi')->default(0)->change();
            $table->integer('detasering')->default(0)->change();
        });
    }
};
