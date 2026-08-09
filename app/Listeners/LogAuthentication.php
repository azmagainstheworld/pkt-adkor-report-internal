<?php

namespace App\Listeners;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthentication
{
    public function handleLogin(Login $event): void
    {
        AuditLog::create([
            'user_id' => $event->user->id,
            'module_key' => 'user',
            'table_name' => 'users',
            'record_id' => $event->user->id,
            'action' => 'login',
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        AuditLog::create([
            'user_id' => $event->user->id ?? null,
            'module_key' => 'user',
            'table_name' => 'users',
            'record_id' => $event->user->id ?? null,
            'action' => 'logout',
        ]);
    }

    // Opsional tapi disarankan untuk keamanan: catat percobaan login yang gagal.
    // user_id bisa null kalau email/username yang dicoba memang tidak terdaftar sama sekali.
    public function handleFailed(Failed $event): void
    {
        $emailDicoba = $event->credentials['email'] ?? $event->credentials['username'] ?? null;
        $user = $emailDicoba ? User::where('email', $emailDicoba)->first() : null;

        AuditLog::create([
            'user_id' => $user->id ?? null,
            'module_key' => 'user',
            'table_name' => 'users',
            'record_id' => $user->id ?? null,
            'action' => 'login_failed',
            'new_value' => $emailDicoba, // simpan email/username yang dicoba, untuk keperluan investigasi keamanan
        ]);
    }
}