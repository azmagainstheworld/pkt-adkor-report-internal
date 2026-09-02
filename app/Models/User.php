<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // <--- HAPUS ATAU KOMENTARI BARIS INI JIKA ADA

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    // DI SINI ADALAH PERUBAHANNYA: HAPUS 'HasApiTokens,'
    // use HasApiTokens, HasFactory, Notifiable; // <--- SEBELUM
    use HasFactory, Notifiable; // <--- SESUDAH (HANYA INI)
    use Auditable;

    protected $auditModuleKey = 'user';
    protected $auditLabelField = 'name';

    // PENTING: last_seen_at berubah di SETIAP request (lewat middleware UpdateUserLastSeen),
    // jadi WAJIB dikecualikan supaya log audit tidak kebanjiran ribuan baris tidak berguna.
    // remember_token & email_verified_at juga bukan perubahan yang perlu diaudit manusia.
    protected $auditExcluded = ['last_seen_at', 'remember_token', 'email_verified_at'];

    // Password tidak pernah dicatat dalam bentuk asli/hash, cukup ditandai berubah
    protected $auditHidden = ['password'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_seen_at',
        'role', // <--- TAMBAHKAN BARIS INI
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Helper untuk mengecek apakah user sedang online
     */
    public function isOnline()
    {
        // Jika last_seen_at kosong, pasti offline
        if (!$this->last_seen_at) {
            return false;
        }

        // Kita anggap online jika aktivitas terakhir kurang dari 5 menit yang lalu
        return $this->last_seen_at->gt(Carbon::now()->subMinutes(5));
    }

    // ==========================================
    // RBAC HELPER METHODS
    // ==========================================

    /**
     * Apakah user adalah Super Admin (level tertinggi, tidak bisa diubah/dihapus).
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Apakah user adalah Admin atau lebih tinggi (Super Admin juga bernilai true).
     * Gunakan ini untuk memproteksi fitur-fitur administratif.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Apakah user adalah Karyawan biasa (level terendah).
     */
    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }
}