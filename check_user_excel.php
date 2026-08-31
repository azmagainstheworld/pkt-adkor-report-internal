<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_perizinan-terbit (1).xlsx';
try {
    $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\PerizinanTerbitImport, $file);
    echo 'Total sheets: ' . count($rows) . "\n";
    if (count($rows) > 0) {
        $firstSheet = $rows[0];
        echo 'Rows: ' . count($firstSheet) . "\n";
        if (count($firstSheet) > 0) {
            echo 'First row data: ';
            print_r($firstSheet[0]);
        }
    }
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
