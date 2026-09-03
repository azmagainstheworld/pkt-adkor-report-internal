<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

$target = "Route::post('/kearsipan/pa-non-teknik/tekstual/master', [PaTekstualController::class, 'storeMaster'])->name('pa-tekstual.storeMaster');";
$replacement = <<<EOF
    Route::post('/kearsipan/pa-non-teknik/tekstual/master', [PaTekstualController::class, 'storeMaster'])->name('pa-tekstual.storeMaster');
    Route::delete('/kearsipan/pa-non-teknik/tekstual/master/{id}', [PaTekstualController::class, 'destroyMaster'])->name('pa-tekstual.destroyMaster');
    Route::post('/kearsipan/pa-non-teknik/tekstual/kolom', [PaTekstualController::class, 'storeKolom'])->name('pa-tekstual.storeKolom');
    Route::delete('/kearsipan/pa-non-teknik/tekstual/kolom/{id}', [PaTekstualController::class, 'destroyKolom'])->name('pa-tekstual.destroyKolom');
EOF;

// Since destroyMaster already exists below storeMaster, let's just replace storeMaster and remove the old destroyMaster to avoid duplicates
$target_remove = "Route::delete('/kearsipan/pa-non-teknik/tekstual/master/{id}', [PaTekstualController::class, 'destroyMaster'])->name('pa-tekstual.destroyMaster');";
$content = str_replace($target_remove, '', $content);
$content = str_replace($target, $replacement, $content);

file_put_contents($file, $content);
echo "Routes added.\n";
