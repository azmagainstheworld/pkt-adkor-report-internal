<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

$search = "Route::get('/program-strategis/export/pdf', [\App\Http\Controllers\ProgramStrategisController::class, 'exportPdf'])->name('program-strategis.export.pdf');";

$replace = $search . "\n    Route::post('/program-strategis/kolom', [\App\Http\Controllers\ProgramStrategisController::class, 'storeKolomDinamis'])->name('program-strategis.kolom.store');\n    Route::delete('/program-strategis/kolom/{id}', [\App\Http\Controllers\ProgramStrategisController::class, 'destroyKolomDinamis'])->name('program-strategis.kolom.destroy');";

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);

$file2 = 'resources/views/program-strategis.blade.php';
$content2 = file_get_contents($file2);

$content2 = str_replace("route('dynamic-column.store')", "route('program-strategis.kolom.store')", $content2);
$content2 = str_replace("route('dynamic-column.destroy'", "route('program-strategis.kolom.destroy'", $content2);

file_put_contents($file2, $content2);

echo "Routes added and blade updated.\n";
