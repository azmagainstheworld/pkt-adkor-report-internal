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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // 1. Sesuaikan panjang karakter name (100)
            $table->string('name', 100);
            
            // 2. Sesuaikan panjang karakter email (150)
            $table->string('email', 150)->unique();
            
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // 3. Tambahkan kolom status (enum) sesuai ERD
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            
            // 4. Tambahkan kolom last_login_at (dateTime) sesuai ERD
            $table->dateTime('last_login_at')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};