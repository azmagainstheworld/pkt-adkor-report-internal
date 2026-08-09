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
        Schema::create('category_colors', function (Blueprint $table) {
            $table->id();
            $table->string('group_name', 100);
            $table->string('category_key', 50);
            $table->string('category_label', 100);
            $table->string('hex_color', 7);
            $table->timestamps(); // ERD hanya menyebut updated_at, tapi timestamps bawaan aman digunakan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_colors');
    }
};
