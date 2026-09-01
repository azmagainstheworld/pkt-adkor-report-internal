<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$dir = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/Bar SK memo';
if (!is_dir($dir)) mkdir($dir, 0777, true);

// Create Terbit.xlsx
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Terbit');
$headers = ['Tahun', 'Bulan', 'SKD Keputusan Bersama', 'SKD Non Ratifikasi', 'SKD Ratifikasi', 'Memo Direksi', 'BAR Monitoring', 'BAR Manajemen'];
$sheet->fromArray($headers, NULL, 'A1');
$dummyDataTerbit = [
    [2024, 'Januari', 0, 4, 2, 4, 1, 0],
    [2024, 'Februari', 0, 4, 3, 4, 0, 0],
    [2024, 'Maret', 0, 6, 7, 1, 1, 0]
];
$sheet->fromArray($dummyDataTerbit, NULL, 'A2');
$writer = new Xlsx($spreadsheet);
$writer->save($dir . '/Terbit.xlsx');

// Create Proses.xlsx
$spreadsheet2 = new Spreadsheet();
$sheet2 = $spreadsheet2->getActiveSheet();
$sheet2->setTitle('Proses');
$headers2 = ['Tahun', 'Bulan', 'Proses SKD Keputusan Bersama', 'Proses SKD Non Ratifikasi', 'Proses SKD Ratifikasi', 'Proses Memo Direksi', 'Proses BAR Monitoring', 'Proses BAR Manajemen'];
$sheet2->fromArray($headers2, NULL, 'A1');
$dummyDataProses = [
    [2024, 'Januari', 0, 4, 2, 4, 1, 0],
    [2024, 'Februari', 0, 4, 3, 4, 0, 0],
    [2024, 'Maret', 0, 6, 7, 1, 1, 0]
];
$sheet2->fromArray($dummyDataProses, NULL, 'A2');
$writer2 = new Xlsx($spreadsheet2);
$writer2->save($dir . '/Proses.xlsx');

echo "Created templates in: " . $dir . "\n";
