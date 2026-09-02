<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // --- TAMBAHKAN KODE INI ---

        // ForceLogoutIfInactive: cek status is_active user di SETIAP request.
        // Kalau akunnya dinonaktifkan admin, user langsung dipaksa logout
        // seketika, tidak perlu menunggu sesi expired.
        // Kita taruh SEBELUM UpdateUserLastSeen supaya user yang sudah
        // dinonaktifkan tidak sempat mengupdate last_seen_at-nya lagi.
        $middleware->web(prepend: [
            \App\Http\Middleware\ForceLogoutIfInactive::class,
        ]);

        // Kita menambahkan Middleware 'UpdateUserLastSeen' ke dalam grup 'web'.
        // Ini berarti setiap kali ada request ke rute web (halaman biasa),
        // middleware ini akan dijalankan untuk mengupdate 'last_seen_at'.
        $middleware->web(append: [
            \App\Http\Middleware\UpdateUserLastSeen::class,
        ]);

        // Alias middleware RBAC untuk digunakan di routes
        $middleware->alias([
            'role.admin' => \App\Http\Middleware\IsAdminOrAbove::class,
            'role.superadmin' => \App\Http\Middleware\IsSuperAdmin::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();