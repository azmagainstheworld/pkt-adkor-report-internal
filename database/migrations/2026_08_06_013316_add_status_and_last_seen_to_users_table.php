<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // boolean: 1 untuk Aktif, 0 untuk Nonaktif. Default Aktif.
            $table->boolean('is_active')->default(true)->after('password');
            // Timestamp untuk melacak aktivitas terakhir
            $table->timestamp('last_seen_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'last_seen_at']);
        });
    }
};