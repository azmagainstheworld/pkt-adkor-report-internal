<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = glob('C:\Users\LENOVO\Downloads\Template_Import_Pelaporan*.xlsx');
if (empty($files)) {
    echo "No template found in Downloads.\n";
    $files = glob('D:\web_pkt_adkor_internal\adkor-report-internal\referensi\*.xlsx');
}
if (!empty($files)) {
    $file = $files[count($files)-1]; // Get latest
    echo "Testing import with: $file\n";
    try {
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PelaporanImport, $file);
        echo "Count in DB: " . \App\Models\Pelaporan::count() . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
