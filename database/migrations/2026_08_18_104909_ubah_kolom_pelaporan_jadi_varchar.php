<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah jadi teks bebas biar nggak ada lagi drama data ditolak MySQL!
        DB::statement('ALTER TABLE pelaporan MODIFY tujuan VARCHAR(255)');
        DB::statement('ALTER TABLE pelaporan MODIFY jenis VARCHAR(255)');
    }

    public function down(): void
    {
        // Kosongin aja sayang~
    }
};