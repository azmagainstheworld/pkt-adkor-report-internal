<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Log bersifat immutable: tidak ada kolom updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'module_key',
        'table_name',
        'record_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Label & warna badge per jenis aksi, dipakai langsung di view
    public function actionLabel(): string
    {
        return match ($this->action) {
            'create' => 'Tambah',
            'update' => 'Ubah',
            'delete' => 'Hapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'login_failed' => 'Login Gagal',
            default => ucfirst($this->action),
        };
    }

    public function actionBadgeClass(): string
    {
        return match ($this->action) {
            'create' => 'bg-green-100 text-green-700 border-green-200',
            'update' => 'bg-blue-100 text-blue-700 border-blue-200',
            'delete' => 'bg-red-100 text-red-700 border-red-200',
            'login' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'logout' => 'bg-gray-100 text-gray-700 border-gray-200',
            'login_failed' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    // Nama pengguna yang aman ditampilkan meski akun user-nya sudah dihapus
    public function actorNameDisplay(): string
    {
        if ($this->user_id === null) {
            return 'Sistem';
        }

        return $this->user->name ?? 'Pengguna Dihapus';
    }
}