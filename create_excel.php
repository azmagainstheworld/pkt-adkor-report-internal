<?php
require __DIR__ . '/vendor/autoload.php';
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Tahun');
$sheet->setCellValue('B1', 'Nama Proses');
$sheet->setCellValue('C1', 'Target');
$sheet->setCellValue('D1', 'Periode');
$sheet->setCellValue('A2', 2026);
$sheet->setCellValue('B2', 'Proses B');
$sheet->setCellValue('C2', 'Selesai');
$sheet->setCellValue('D2', 'Maret');
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save(__DIR__ . '/scratch/test_proses.xlsx');
echo "Excel created";
