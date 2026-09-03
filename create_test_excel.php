<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$filePath = 'd:/web_pkt_adkor_internal/adkor-report-internal/referensi/Template_Pengiriman_Biaya_Ongkir.xlsx';
$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();

// Add a row of data
$worksheet->setCellValue('A2', 2026);
$worksheet->setCellValue('B2', 'Januari');
$worksheet->setCellValue('C2', 500000);
$worksheet->setCellValue('D2', 100000);

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$testFile = 'd:/web_pkt_adkor_internal/adkor-report-internal/referensi/Template_Pengiriman_Biaya_Ongkir_Filled.xlsx';
$writer->save($testFile);

echo "Created test file.\n";
