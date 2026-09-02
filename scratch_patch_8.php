<?php

// 1. Create IsSuperAdmin Middleware
$middlewarePath = 'app/Http/Middleware/IsSuperAdmin.php';
$middlewareCode = <<<PHP
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    public function handle(Request \$request, Closure \$next): Response
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang dapat mengakses halaman ini.');
        }

        return \$next(\$request);
    }
}

PHP;
file_put_contents($middlewarePath, $middlewareCode);
echo "Created IsSuperAdmin.php\n";

// 2. Register Middleware in bootstrap/app.php
$bootstrapPath = 'bootstrap/app.php';
$bootstrapContent = file_get_contents($bootstrapPath);
if (strpos($bootstrapContent, "'role.superadmin'") === false) {
    $bootstrapContent = str_replace(
        "'role.admin' => \App\Http\Middleware\IsAdminOrAbove::class,",
        "'role.admin' => \App\Http\Middleware\IsAdminOrAbove::class,\n            'role.superadmin' => \App\Http\Middleware\IsSuperAdmin::class,",
        $bootstrapContent
    );
    file_put_contents($bootstrapPath, $bootstrapContent);
    echo "Registered role.superadmin in bootstrap/app.php\n";
}

// 3. Update layouts/app.blade.php to hide Manajemen Pengguna for non-superadmins
$appBladePath = 'resources/views/layouts/app.blade.php';
$appBladeContent = file_get_contents($appBladePath);
if (strpos($appBladeContent, '@if(auth()->check() && auth()->user()->isSuperAdmin())') === false) {
    // Only wrap Manajemen Pengguna
    $searchMenu = <<<HTML
                        <!-- Manajemen Pengguna -->
                        <a href="/admin/manajemen-pengguna" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/manajemen-pengguna') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Manajemen Pengguna
                        </a>
HTML;
    $replaceMenu = <<<HTML
                        @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <!-- Manajemen Pengguna -->
                        <a href="/admin/manajemen-pengguna" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/manajemen-pengguna') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Manajemen Pengguna
                        </a>
                        @endif
HTML;
    $appBladeContent = str_replace($searchMenu, $replaceMenu, $appBladeContent);
    file_put_contents($appBladePath, $appBladeContent);
    echo "Wrapped Manajemen Pengguna in layouts/app.blade.php\n";
}

// 4. Update routes/web.php
$webPhpPath = 'routes/web.php';
$webPhpContent = file_get_contents($webPhpPath);

// Wrap admin/manajemen-pengguna
$adminRoutesSearch = <<<PHP
    // ==========================================
    // MANAJEMEN PENGGUNA (ADMIN)
    // ==========================================
    Route::get('/admin/manajemen-pengguna', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/manajemen-pengguna/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/manajemen-pengguna', [UserController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/manajemen-pengguna/{user}/toggle', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');
    Route::patch('/admin/manajemen-pengguna/{user}/password', [UserController::class, 'changePassword'])->name('admin.users.password.update');
    Route::delete('/admin/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
PHP;

$adminRoutesReplace = <<<PHP
    // ==========================================
    // MANAJEMEN PENGGUNA (ADMIN)
    // ==========================================
    Route::middleware('role.superadmin')->group(function () {
        Route::get('/admin/manajemen-pengguna', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/manajemen-pengguna/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/manajemen-pengguna', [UserController::class, 'store'])->name('admin.users.store');
        Route::patch('/admin/manajemen-pengguna/{user}/toggle', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');
        Route::patch('/admin/manajemen-pengguna/{user}/password', [UserController::class, 'changePassword'])->name('admin.users.password.update');
        Route::delete('/admin/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });
PHP;

if (strpos($webPhpContent, "middleware('role.superadmin')") === false) {
    $webPhpContent = str_replace($adminRoutesSearch, $adminRoutesReplace, $webPhpContent);
}

// Protect all kolom-dinamis routes with role.admin
// using regex: look for Route::post or Route::delete containing 'kolom-dinamis' that do not already have ->middleware('role.admin')
$webPhpContent = preg_replace('/(Route::(?:post|delete)\([^\)]*kolom-dinamis[^\)]*\)[^\;]*?)(?<!middleware\(\'role\.admin\'\));/', "$1->middleware('role.admin');", $webPhpContent);

file_put_contents($webPhpPath, $webPhpContent);
echo "Updated routes/web.php\n";

// 5. Update karyawan.blade.php
$karyawanPath = 'resources/views/karyawan.blade.php';
$karyawanContent = file_get_contents($karyawanPath);
$karyawanSearch = <<<HTML
                        <button type="button" onclick="openModal('modalAturKolomTabel'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Atur Kolom Tabel
                        </button>
                        <button type="button" onclick="openModal('modalAturKolomProfil'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Atur Kolom Profil
                        </button>
HTML;
$karyawanReplace = <<<HTML
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <button type="button" onclick="openModal('modalAturKolomTabel'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Atur Kolom Tabel
                        </button>
                        <button type="button" onclick="openModal('modalAturKolomProfil'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Atur Kolom Profil
                        </button>
                        @endif
HTML;
if (strpos($karyawanContent, '@if(auth()->check() && auth()->user()->isAdmin())') === false) {
    $karyawanContent = str_replace($karyawanSearch, $karyawanReplace, $karyawanContent);
    file_put_contents($karyawanPath, $karyawanContent);
    echo "Updated karyawan.blade.php\n";
}

// 6. Update ketidakhadiran/index.blade.php
$ketidakhadiranPath = 'resources/views/ketidakhadiran/index.blade.php';
$ketidakhadiranContent = file_get_contents($ketidakhadiranPath);
$khSearch = <<<HTML
                    <button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Harian
                    </button>
HTML;
$khReplace = <<<HTML
                    @if(auth()->check() && auth()->user()->isAdmin())
                    <button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Harian
                    </button>
                    @endif
HTML;
if (strpos($ketidakhadiranContent, '@if(auth()->check() && auth()->user()->isAdmin())') === false) {
    $ketidakhadiranContent = str_replace($khSearch, $khReplace, $ketidakhadiranContent);
    file_put_contents($ketidakhadiranPath, $ketidakhadiranContent);
    echo "Updated ketidakhadiran/index.blade.php\n";
}

?>
