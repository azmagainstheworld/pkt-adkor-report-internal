<?php

use Illuminate\Support\Facades\Broadcast;

// Membuat 'Presence Channel' khusus untuk melacak user yang sedang online
Broadcast::channel('online-users', function ($user) {
    // Jika user punya akun (terautentikasi), kembalikan data ID dan Nama-nya ke channel
    if ($user) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});