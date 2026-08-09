<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menyesuaikan tabel audit_logs yang SUDAH ADA (dibuat 22 Juli), bukan membuat tabel baru.
     * Perubahan:
     * 1. Tambah opsi 'login', 'logout', 'login_failed' ke enum action.
     * 2. Jadikan user_id, table_name, record_id nullable (login/logout tidak selalu
     *    terkait record tertentu, dan login gagal tidak selalu punya user_id valid).
     * 3. Ganti FK user_id dari ON DELETE CASCADE -> ON DELETE SET NULL, supaya riwayat
     *    log TIDAK ikut terhapus kalau akun user-nya suatu saat dihapus dari sistem
     *    (prinsip dasar audit trail: histori harus tetap ada).
     */
    public function up()
    {
        DB::statement("ALTER TABLE audit_logs MODIFY COLUMN action ENUM('create','update','delete','login','logout','login_failed') NOT NULL");

        DB::statement("ALTER TABLE audit_logs MODIFY COLUMN user_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE audit_logs MODIFY COLUMN table_name VARCHAR(100) NULL");
        DB::statement("ALTER TABLE audit_logs MODIFY COLUMN record_id BIGINT NULL");

        DB::statement("ALTER TABLE audit_logs DROP FOREIGN KEY audit_logs_user_id_foreign");
        DB::statement("ALTER TABLE audit_logs ADD CONSTRAINT audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE audit_logs DROP FOREIGN KEY audit_logs_user_id_foreign");
        DB::statement("ALTER TABLE audit_logs ADD CONSTRAINT audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");

        DB::statement("ALTER TABLE audit_logs MODIFY COLUMN record_id BIGINT NOT NULL");
        DB::statement("ALTER TABLE audit_logs MODIFY COLUMN table_name VARCHAR(100) NOT NULL");
        DB::statement("ALTER TABLE audit_logs MODIFY COLUMN user_id BIGINT UNSIGNED NOT NULL");

        DB::statement("ALTER TABLE audit_logs MODIFY COLUMN action ENUM('create','update','delete') NOT NULL");
    }
};