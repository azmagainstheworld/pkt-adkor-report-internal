<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_perizinan-terbit (1).xlsx';
try {
    $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\PerizinanTerbitImport, $file);
    $data = $rows[0];
    
    $emptyNomor = 0;
    $uniqueNomor = [];
    
    foreach ($data as $row) {
        if (!isset($row['nomor']) || trim($row['nomor']) === '') {
            $emptyNomor++;
        } else {
            $uniqueNomor[$row['nomor']] = true;
        }
    }
    
    echo "Total rows in sheet: " . count($data) . "\n";
    echo "Rows with empty nomor: $emptyNomor\n";
    echo "Unique nomor count: " . count($uniqueNomor) . "\n";
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
