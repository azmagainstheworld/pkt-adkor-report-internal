<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('dynamic_columns', function (Blueprint $table) {
            $table->id();
            $table->string('modul'); // Untuk mendata ini kolom milik menu apa
            $table->string('nama_kolom'); // Nama kolom yang diinput Klien
            $table->string('tipe_input'); // text, number, date, dropdown
            $table->json('pilihan_dropdown')->nullable(); // Isi pilihan dropdown
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('dynamic_columns');
    }
};