<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$files = [
    'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\DOF\Template_dof-1 (2).xlsx',
    'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\DOF\Template_dof-2.xlsx'
];

foreach ($files as $file) {
    echo "====================================\n";
    echo "FILE: " . basename($file) . "\n";
    $spreadsheet = IOFactory::load($file);
    $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    
    // Dump first 5 rows
    print_r(array_slice($data, 0, 5));
    echo "\n";
}
