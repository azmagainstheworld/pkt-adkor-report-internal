<?php

$controllerFile = 'app/Http/Controllers/PemeliharaanController.php';
$content = file_get_contents($controllerFile);

$target = "Excel::import(new \App\Imports\PemeliharaanRutinImport, \$request->file('file_excel'));";
$replacement = "\Illuminate\Support\Facades\Log::info('Import Rutin web UI triggered with file: ' . \$request->file('file_excel')->getClientOriginalName());\n            " . $target;

if (strpos($content, $replacement) === false) {
    $content = str_replace($target, $replacement, $content);
    file_put_contents($controllerFile, $content);
    echo "Controller logged.\n";
} else {
    echo "Already logged.\n";
}
