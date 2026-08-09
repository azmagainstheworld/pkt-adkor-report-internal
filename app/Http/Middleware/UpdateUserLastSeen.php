<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class UpdateUserLastSeen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sudah login
        if (Auth::check()) {
            // Update database secara 'silent' (tanpa mengubah updated_at bawaan)
            // Ini membutuhkan kolom 'last_seen_at' di tabel users.
            // Pastikan Anda sudah menjalankan migration untuk menambahkannya.
            Auth::user()->timestamps = false;
            Auth::user()->update([
                'last_seen_at' => Carbon::now()
            ]);
        }

        return $next($request);
    }
}