<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_perizinan-terbit (1).xlsx';
try {
    $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\PerizinanTerbitImport, $file);
    $data = $rows[0];
    
    $duplicates = [];
    foreach ($data as $i => $row) {
        if (!isset($row['nomor']) || trim($row['nomor']) === '') continue;
        $duplicates[$row['nomor']][] = $i;
    }
    
    $dupCount = 0;
    foreach ($duplicates as $nomor => $indices) {
        if (count($indices) > 1) {
            echo "Nomor '$nomor' appears at rows: " . implode(', ', $indices) . "\n";
            echo "Row 1:\n";
            print_r($data[$indices[0]]);
            echo "Row 2:\n";
            print_r($data[$indices[1]]);
            $dupCount++;
            if ($dupCount >= 2) break; // just show 2 examples
        }
    }
    
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
