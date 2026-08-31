<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file1 = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_dof-1 (2).xlsx';
$file2 = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_dof-2.xlsx';

try {
    echo "Importing Tabel 1...\n";
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\DofImport(1), $file1);
    echo "Tabel 1 Import script finished without throwing an exception.\n";
    
    echo "Importing Tabel 2...\n";
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\DofImport(2), $file2);
    echo "Tabel 2 Import script finished without throwing an exception.\n";
    
    echo "Count in DB: " . \App\Models\DofData::count() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
} catch (\Throwable $e) {
    echo "Throwable: " . $e->getMessage() . "\n";
}
