<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_perizinan-terbit (1).xlsx';
echo "Checking file: $file\n";

if (!file_exists($file)) {
    echo "File not found!\n";
    exit;
}

try {
    $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\PerizinanTerbitImport, $file);
    echo "Total sheets: " . count($rows) . "\n";
    if (count($rows) > 0) {
        $firstSheet = $rows[0];
        echo "Rows in first sheet: " . count($firstSheet) . "\n";
        if (count($firstSheet) > 0) {
            echo "First row data:\n";
            print_r($firstSheet[0]);
        }
    }
} catch (\Exception $e) {
    echo "Error parsing Excel: " . $e->getMessage() . "\n";
}
