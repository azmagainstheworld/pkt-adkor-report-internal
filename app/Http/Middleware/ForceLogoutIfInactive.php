<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForceLogoutIfInactive
{
    /**
     * Cek status is_active user di SETIAP request. Kalau user sudah login tapi
     * akunnya dinonaktifkan admin (is_active = false), paksa logout seketika
     * — jangan tunggu sesi expired atau user logout manual sendiri.
     *
     * Catatan: kalau user benar-benar DIHAPUS (bukan dinonaktifkan), Laravel
     * sudah otomatis menangani ini tanpa middleware tambahan, karena
     * Auth::user() otomatis jadi null saat row-nya sudah tidak ada.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->is_active) {
            $wasName = Auth::user()->name;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', "Akun \"{$wasName}\" telah dinonaktifkan oleh administrator.");
        }

        return $next($request);
    }
}