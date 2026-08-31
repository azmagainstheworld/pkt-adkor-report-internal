<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mantra sakti buat ngubah kolom ENUM jadi teks bebas (VARCHAR)
        // Biar tanda "-" atau kata status apapun bisa masuk dengan mulus!
        DB::statement('ALTER TABLE program_strategis MODIFY status VARCHAR(255) DEFAULT "-"');
    }

    public function down(): void
    {
        // Kosongin aja sayang, kita maju terus pantang mundur!
    }
};