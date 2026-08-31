<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$dir = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/Teknik';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// ==========================================
// TABEL 1
// ==========================================
$spreadsheet1 = new Spreadsheet();
$sheet1 = $spreadsheet1->getActiveSheet();
$sheet1->setTitle('Tabel 1');

$headers1 = [
    'Tahun', 'Bulan', 'Alih Media (berkas/lembar)', 'Jasa Cetak Gambar (berkas)',
    'Peminjaman Dok. (berkas/lembar)', 'Peminjaman Dok. (bantex)', 'Peminjaman Dok. (CD)',
    'Peminjaman Dok. (lembar)', 'Permintaan Copy/Soft (berkas/lembar)', 'Konversi TIF ke PDF (File)'
];

$rowIdx = 1;
foreach ($headers1 as $colIdx => $header) {
    $sheet1->setCellValueByColumnAndRow($colIdx + 1, $rowIdx, $header);
}

$data1 = [
    [2024, 'Januari', 2977, 0, 0, 0, 0, 0, 2457, 0],
    [2024, 'Februari', 7146, 271, 0, 0, 0, 0, 2422, 0],
    [2024, 'Maret', 5872, 51, 0, 0, 0, 0, 1288, 0],
    [2024, 'April', 3715, 0, 0, 0, 0, 0, 5378, 0],
    [2024, 'Mei', 3506, 0, 0, 0, 0, 0, 36257, 0],
    [2024, 'Juni', 0, 229, 0, 0, 0, 0, 15575, 0],
    [2024, 'Juli', 1562, 104, 0, 0, 0, 0, 677, 0],
    [2024, 'Agustus', 3412, 622, 0, 0, 0, 0, 10065, 0],
    [2024, 'September', 1686, 267, 0, 0, 0, 0, 23356, 0],
    [2024, 'Oktober', 1394, 352, 0, 0, 0, 0, 4098, 0],
    [2024, 'November', 1939, 0, 0, 0, 0, 0, 2470, 0],
    [2024, 'Desember', 3167, 25, 0, 0, 0, 0, 150, 0],
];

$rowIdx = 2;
foreach ($data1 as $rowData) {
    foreach ($rowData as $colIdx => $value) {
        $sheet1->setCellValueByColumnAndRow($colIdx + 1, $rowIdx, $value);
    }
    $rowIdx++;
}

$writer1 = new Xlsx($spreadsheet1);
$writer1->save($dir . '/tabel 1.xlsx');
echo "tabel 1.xlsx created.\n";


// ==========================================
// TABEL 2
// ==========================================
$spreadsheet2 = new Spreadsheet();
$sheet2 = $spreadsheet2->getActiveSheet();
$sheet2->setTitle('Tabel 2');

$headers2 = [
    'Tahun', 'Bulan', 'Penyerahan Dok. (berkas/lembar)', 'Penyerahan Dok. (bantex)',
    'Penyerahan Dok. (CD)', 'Upload Dokumen TF ke Smartshare',
    'Alih Media Dokumen Proyek (Sodaash. Revamping, Turn Arround, Crash Program, Procurement)',
    'Rekapitulasi Dokumen Proyek (Sodaash. Revamping, Turn Arround, Crash Program, Procurement)'
];

$rowIdx = 1;
foreach ($headers2 as $colIdx => $header) {
    $sheet2->setCellValueByColumnAndRow($colIdx + 1, $rowIdx, $header);
}

$data2 = [
    [2024, 'Januari', 7, 0, 0, 6628, 0, 0],
    [2024, 'Februari', 39, 0, 0, 5127, 0, 0],
    [2024, 'Maret', 0, 0, 0, 0, 0, 0],
    [2024, 'April', 0, 0, 0, 0, 0, 0],
    [2024, 'Mei', 0, 0, 0, 0, 0, 0],
    [2024, 'Juni', 0, 0, 0, 0, 0, 0],
    [2024, 'Juli', 0, 0, 0, 0, 0, 0],
    [2024, 'Agustus', 0, 0, 0, 0, 0, 0],
    [2024, 'September', 0, 0, 0, 0, 0, 0],
    [2024, 'Oktober', 0, 0, 0, 0, 0, 0],
    [2024, 'November', 0, 0, 0, 0, 0, 0],
    [2024, 'Desember', 0, 0, 0, 0, 0, 0],
];

$rowIdx = 2;
foreach ($data2 as $rowData) {
    foreach ($rowData as $colIdx => $value) {
        $sheet2->setCellValueByColumnAndRow($colIdx + 1, $rowIdx, $value);
    }
    $rowIdx++;
}

$writer2 = new Xlsx($spreadsheet2);
$writer2->save($dir . '/tabel 2.xlsx');
echo "tabel 2.xlsx created.\n";
