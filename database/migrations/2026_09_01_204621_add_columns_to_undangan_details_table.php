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
        Schema::table('undangan_details', function (Blueprint $table) {
            $table->foreignId('undangan_id')->constrained('undangan')->onDelete('cascade');
            $table->enum('jenis_undangan', ['Internal', 'Eksternal']);
            $table->text('agenda')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('undangan_details', function (Blueprint $table) {
            $table->dropForeign(['undangan_id']);
            $table->dropColumn(['undangan_id', 'jenis_undangan', 'agenda']);
        });
    }
};
