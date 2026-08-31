<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ganti nama tabel dari bahasa campuran jadi bahasa Indonesia murni
        if (Schema::hasTable('skd_terbits') && !Schema::hasTable('skd_terbit')) {
            Schema::rename('skd_terbits', 'skd_terbit');
        }
        
        if (Schema::hasTable('memo_terbits') && !Schema::hasTable('memo_terbit')) {
            Schema::rename('memo_terbits', 'memo_terbit');
        }
    }

    public function down(): void
    {
        // Kembalikan nama tabel kalau perlu di-rollback
        if (Schema::hasTable('skd_terbit') && !Schema::hasTable('skd_terbits')) {
            Schema::rename('skd_terbit', 'skd_terbits');
        }
        
        if (Schema::hasTable('memo_terbit') && !Schema::hasTable('memo_terbits')) {
            Schema::rename('memo_terbit', 'memo_terbits');
        }
    }
};