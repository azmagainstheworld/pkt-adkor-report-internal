<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Worksheet');

// ============ HEADER ROW ============
// These column names MUST match what JasaFotocopyImport expects
// Import reads: tahun, bulan, unit_kerja, cost_centre, pemakaian_lbr, fee_lbr, sewa_bln
$headers = [
    'A' => 'tahun',
    'B' => 'bulan',
    'C' => 'unit_kerja',
    'D' => 'cost_centre',
    'E' => 'keterangan',
    'F' => 'tipe_mesin',
    'G' => 'pemakaian_lbr',
    'H' => 'fee_lbr',
    'I' => 'sewa_bln',
];

foreach ($headers as $col => $header) {
    $sheet->setCellValue($col . '1', $header);
}

// Header style - orange like the app
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF'], 'size' => 10],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'EA580C']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'CCCCCC']]],
];
$sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(20);

// ============ DATA ROWS (13 baris) ============
$unitKerjaList = [
    'Dept. Produksi Amoniak',
    'Dept. Produksi Urea',
    'Dept. Utilitas',
    'Dept. Pemeliharaan',
    'Dept. K3',
    'Dept. HR & GA',
    'Dept. Keuangan',
    'Dept. Pengadaan',
    'Dept. Pemasaran',
    'Dept. IT',
    'Dept. Hukum',
    'Dept. Hubungan Masyarakat',
    'Dept. Lingkungan Hidup',
];
$costCentres = ['1010','1020','1030','1040','1050','1060','1070','1080','1090','1100','1110','1120','1130'];
$typeMesin = [
    'RICOH MP C3004', 'RICOH MP 4504', 'XEROX AltaLink C8055',
    'CANON imageRUNNER 2625', 'FUJI FILM ApeosPort 4560'
];
$keteranganList = ['KOPKAR', 'KOPKAR', 'PKT', 'KOPKAR', 'PKT', 'KOPKAR', 'PKT', 'KOPKAR', 'PKT', 'KOPKAR', 'PKT', 'KOPKAR', 'PKT'];

$tahun = 2024;
$bulan = 'Agustus';
$feePerLembar = 50.00;
$sewaBulanan = 1500000.00;

for ($i = 0; $i < 13; $i++) {
    $row = $i + 2;
    $pemakaianLembar = rand(800, 3500);

    $sheet->setCellValue('A' . $row, $tahun);
    $sheet->setCellValue('B' . $row, $bulan);
    $sheet->setCellValue('C' . $row, $unitKerjaList[$i]);
    $sheet->setCellValue('D' . $row, $costCentres[$i]);
    $sheet->setCellValue('E' . $row, $keteranganList[$i]);
    $sheet->setCellValue('F' . $row, $typeMesin[$i % count($typeMesin)]);
    $sheet->setCellValue('G' . $row, $pemakaianLembar);
    $sheet->setCellValue('H' . $row, $feePerLembar);
    $sheet->setCellValue('I' . $row, $sewaBulanan);

    // Alternating row color
    $rowColor = ($i % 2 === 0) ? 'FFFFFF' : 'FFF7ED';
    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowColor]],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'E5E7EB']]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    ]);
}

// Auto-size columns
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Save
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$outputPath = 'referensi/fotocopy/Template_jasa-fotocopy_import.xlsx';
$writer->save($outputPath);
echo "Template tersimpan: $outputPath\n";
echo "13 baris data siap import.\n";

// Also copy as the system template
copy($outputPath, 'storage/app/templates/jasa-fotocopy.xlsx');
echo "Juga disalin ke storage/app/templates/jasa-fotocopy.xlsx\n";
