<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman_costs', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('bulan');
            // Gunakan BigInt untuk nominal mata uang Rupiah tanpa pecahan sen
            $table->bigInteger('cost_domestik')->default(0);
            $table->bigInteger('cost_internasional')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman_costs');
    }
};