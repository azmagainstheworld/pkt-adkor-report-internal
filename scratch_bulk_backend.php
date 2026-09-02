<?php
// Update KaryawanController
$content = file_get_contents('app/Http/Controllers/KaryawanController.php');
if (strpos($content, 'function destroyBulk') === false) {
    $bulkMethod = <<<EOD

    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            \App\Models\Karyawan::whereIn('id', \$ids)->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }
EOD;
    // Insert before the last brace
    $content = preg_replace('/\}\s*$/', $bulkMethod . "\n}", $content);
    file_put_contents('app/Http/Controllers/KaryawanController.php', $content);
    echo "Updated KaryawanController\n";
}

// Update AnggaranController
$content = file_get_contents('app/Http/Controllers/AnggaranController.php');
if (strpos($content, 'function destroyBulk') === false) {
    $bulkMethod = <<<EOD

    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            \App\Models\AnggaranAdministrasi::whereIn('id', \$ids)->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }
EOD;
    $content = preg_replace('/\}\s*$/', $bulkMethod . "\n}", $content);
    file_put_contents('app/Http/Controllers/AnggaranController.php', $content);
    echo "Updated AnggaranController\n";
}

// Update KetidakhadiranController
$content = file_get_contents('app/Http/Controllers/KetidakhadiranController.php');
if (strpos($content, 'function destroyBulananBulk') === false) {
    $bulkMethod = <<<EOD

    public function destroyBulananBulk(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            \App\Models\Ketidakhadiran::whereIn('id', \$ids)->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }

    public function destroyHarianBulk(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            \App\Models\KetidakhadiranHarian::whereIn('id', \$ids)->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }
EOD;
    $content = preg_replace('/\}\s*$/', $bulkMethod . "\n}", $content);
    file_put_contents('app/Http/Controllers/KetidakhadiranController.php', $content);
    echo "Updated KetidakhadiranController\n";
}

// Update web.php
$web = file_get_contents('routes/web.php');
$changes = 0;
if (strpos($web, "Route::delete('/karyawan-bulk/destroy'") === false) {
    $web = str_replace("Route::resource('karyawan', KaryawanController::class);", "Route::resource('karyawan', KaryawanController::class);\n    Route::delete('/karyawan-bulk/destroy', [KaryawanController::class, 'destroyBulk'])->name('karyawan.destroyBulk');", $web);
    $changes++;
}
if (strpos($web, "Route::delete('/ketidakhadiran/bulanan-bulk/destroy'") === false) {
    $web = str_replace("Route::delete('/ketidakhadiran/bulanan/{ketidakhadiran}', [KetidakhadiranController::class, 'destroyBulanan'])\n        ->name('ketidakhadiran.destroyBulanan');", "Route::delete('/ketidakhadiran/bulanan/{ketidakhadiran}', [KetidakhadiranController::class, 'destroyBulanan'])\n        ->name('ketidakhadiran.destroyBulanan');\n    Route::delete('/ketidakhadiran/bulanan-bulk/destroy', [KetidakhadiranController::class, 'destroyBulananBulk'])->name('ketidakhadiran.destroyBulananBulk');", $web);
    $changes++;
}
if (strpos($web, "Route::delete('/ketidakhadiran/harian-bulk/destroy'") === false) {
    $web = str_replace("Route::delete('/ketidakhadiran/harian/{harian}', [KetidakhadiranController::class, 'destroyHarian'])\n        ->name('ketidakhadiran.destroyHarian');", "Route::delete('/ketidakhadiran/harian/{harian}', [KetidakhadiranController::class, 'destroyHarian'])\n        ->name('ketidakhadiran.destroyHarian');\n    Route::delete('/ketidakhadiran/harian-bulk/destroy', [KetidakhadiranController::class, 'destroyHarianBulk'])->name('ketidakhadiran.destroyHarianBulk');", $web);
    $changes++;
}
if (strpos($web, "Route::delete('/anggaran-bulk/destroy'") === false) {
    $web = str_replace("Route::delete('/anggaran/{anggaran}', [AnggaranController::class, 'destroy'])->name('anggaran.destroy');", "Route::delete('/anggaran/{anggaran}', [AnggaranController::class, 'destroy'])->name('anggaran.destroy');\n    Route::delete('/anggaran-bulk/destroy', [AnggaranController::class, 'destroyBulk'])->name('anggaran.destroyBulk');", $web);
    $changes++;
}
if ($changes > 0) {
    file_put_contents('routes/web.php', $web);
    echo "Updated routes/web.php\n";
}

echo "Done Backend.\n";
