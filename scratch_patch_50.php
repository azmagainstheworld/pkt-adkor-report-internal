<?php
$path = 'routes/web.php';
$content = file_get_contents($path);

// Find the line with Route::delete('/pelaporan-bulk/destroy'...
$search = "Route::delete('/pelaporan-bulk/destroy', [PelaporanController::class, 'destroyBulk'])->name('pelaporan.destroyBulk');";
$replace = $search . "\n    Route::get('/pelaporan-bulk/destroy', function() { return redirect()->route('pelaporan.index'); });";

$content = str_replace($search, $replace, $content);

file_put_contents($path, $content);
echo "Added fallback GET route!\n";
?>
