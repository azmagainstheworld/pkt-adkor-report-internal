<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a dummy excel file to test
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Tahun');
$sheet->setCellValue('B1', 'Bulan');
$sheet->setCellValue('C1', 'Surat Masuk');
$sheet->setCellValue('D1', 'Surat Keluar');

$sheet->setCellValue('A2', '2025');
$sheet->setCellValue('B2', 'Januari');
$sheet->setCellValue('C2', '10');
$sheet->setCellValue('D2', '5');

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('test_rekap.xlsx');

echo "Excel created.\n";

try {
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SuratRekapImport, 'test_rekap.xlsx');
    echo "Import success.\n";
    echo "Data in DB: " . json_encode(\App\Models\SuratRekap::all()) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
