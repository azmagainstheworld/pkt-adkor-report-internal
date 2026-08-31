<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_perizinan-terbit (1).xlsx';
try {
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PerizinanTerbitImport, $file);
    echo "Import script finished without throwing an exception.\n";
    echo "Count in DB: " . \App\Models\PerizinanTerbit::count() . "\n";
} catch (\Exception $e) {
    echo 'Error during import: ' . $e->getMessage() . "\n";
}
