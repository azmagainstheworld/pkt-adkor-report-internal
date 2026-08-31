<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = [
    'Tahun',
    'Bulan',
    'Kategori',
    'Detail Anggaran',
    'RKAP',
    'Komitmen',
    'Realisasi',
    'Keterangan'
];

// Write headers
foreach ($headers as $index => $header) {
    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
    $sheet->setCellValue($column . '1', $header);
}

// Sample row
$sheet->setCellValue('A2', '2026');
$sheet->setCellValue('B2', 'Januari');
$sheet->setCellValue('C2', 'Dikelola');
$sheet->setCellValue('D2', 'Contoh Detail Anggaran Dikelola');
$sheet->setCellValue('E2', '100000000');
$sheet->setCellValue('F2', '20000000');
$sheet->setCellValue('G2', '50000000');
$sheet->setCellValue('H2', 'Contoh Keterangan');

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/referensi/Template_anggaran_BENAR.xlsx');

echo "Done\n";
