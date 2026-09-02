<?php
$path = 'routes/web.php';
$content = file_get_contents($path);

// Add fallback route for pengiriman-dokumen-bulk/destroy
$search = "Route::delete('/administrasi/pengiriman-dokumen-bulk/destroy', [PengirimanDokumenController::class, 'destroyBulk'])->name('pengiriman-dokumen.destroyBulk');";
$replace = $search . "\n    Route::get('/administrasi/pengiriman-dokumen-bulk/destroy', function() { return redirect()->route('pengiriman-dokumen.index'); });";

if (strpos($content, "Route::get('/administrasi/pengiriman-dokumen-bulk/destroy'") === false) {
    $content = str_replace($search, $replace, $content);
    file_put_contents($path, $content);
    echo "Fallback route added!\n";
} else {
    echo "Fallback route already exists.\n";
}
?>
