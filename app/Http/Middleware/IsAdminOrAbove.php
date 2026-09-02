<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminOrAbove
{
    /**
     * Memastikan user yang mengakses adalah Admin atau Super Admin.
     * Karyawan biasa akan mendapatkan response 403 Forbidden.
     *
     * Hierarki:
     *   super_admin > admin > karyawan
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya Admin atau Super Admin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
