<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_perizinan-proses.xlsx';
try {
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PerizinanProsesImport, $file);
    echo "Success! Count in DB: " . \App\Models\PerizinanProsesList::count() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
