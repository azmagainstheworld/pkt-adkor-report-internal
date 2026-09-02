<?php

$webPhpPath = 'routes/web.php';
$webPhpContent = file_get_contents($webPhpPath);

$search1 = "Route::post('/program-strategis/kolom', [\App\Http\Controllers\ProgramStrategisController::class, 'storeKolomDinamis'])->name('program-strategis.kolom.store');";
$replace1 = "Route::post('/program-strategis/kolom', [\App\Http\Controllers\ProgramStrategisController::class, 'storeKolomDinamis'])->name('program-strategis.kolom.store')->middleware('role.admin');";

$search2 = "Route::delete('/program-strategis/kolom/{id}', [\App\Http\Controllers\ProgramStrategisController::class, 'destroyKolomDinamis'])->name('program-strategis.kolom.destroy');";
$replace2 = "Route::delete('/program-strategis/kolom/{id}', [\App\Http\Controllers\ProgramStrategisController::class, 'destroyKolomDinamis'])->name('program-strategis.kolom.destroy')->middleware('role.admin');";

if (strpos($webPhpContent, $search1) !== false) {
    $webPhpContent = str_replace($search1, $replace1, $webPhpContent);
    echo "Added role.admin middleware to program-strategis.kolom.store\n";
} else {
    echo "Could not find program-strategis.kolom.store\n";
}

if (strpos($webPhpContent, $search2) !== false) {
    $webPhpContent = str_replace($search2, $replace2, $webPhpContent);
    echo "Added role.admin middleware to program-strategis.kolom.destroy\n";
} else {
    echo "Could not find program-strategis.kolom.destroy\n";
}

file_put_contents($webPhpPath, $webPhpContent);

?>
