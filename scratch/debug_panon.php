<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Types in DB:\n";
foreach(App\Models\PaNonTekstualType::all() as $t) {
    echo "'{$t->name}'\n";
}

$file2 = 'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\non teknik\NON teknik Non tekstual.xlsx';
if(file_exists($file2)) {
    echo "\nHeaders in Excel 2:\n";
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file2);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    $headers = $rows[0];
    foreach($headers as $h) {
        if(!empty($h)) {
            echo "'" . trim($h) . "'\n";
        }
    }
}
