<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$file = 'referensi/fotocopy/Template_jasa-fotocopy (4).xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();

// Data dummy 13 baris - realistic fotocopy data
$unitKerjaList = [
    'Dept. Produksi Amoniak', 'Dept. Produksi Urea', 'Dept. Utilitas',
    'Dept. Pemeliharaan', 'Dept. K3', 'Dept. HR & GA', 'Dept. Keuangan',
    'Dept. Pengadaan', 'Dept. Pemasaran', 'Dept. IT', 'Dept. Hukum',
    'Dept. Hubungan Masyarakat', 'Dept. Lingkungan Hidup'
];
$costCentres = [
    '1010', '1020', '1030', '1040', '1050', '1060', '1070',
    '1080', '1090', '1100', '1110', '1120', '1130'
];
$typeMesin = ['RICOH MP C3004', 'RICOH MP 4504', 'XEROX AltaLink C8055', 'CANON imageRUNNER 2625', 'FUJI FILM ApeosPort 4560'];
$bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

$tahun = 2024;
$feePerLembar = 50; // Rp 50/lembar
$sewaBulanan = 1500000; // Rp 1.5jt/bulan
$bulanIdx = 7; // Agustus

for ($i = 0; $i < 13; $i++) {
    $row = $i + 2;
    $bulan = $bulanList[$bulanIdx];
    
    // Jumlah pemakaian realistis
    $pemakaianBulanIni = rand(800, 3500);
    $pemakaianSdBulanIni = $pemakaianBulanIni + rand(4000, 15000);
    
    $biayaFeeBulanIni = $pemakaianBulanIni * $feePerLembar;
    $biayaFeeSdBulanIni = $pemakaianSdBulanIni * $feePerLembar;
    $totalSewaDanFeeBulanIni = $sewaBulanan + $biayaFeeBulanIni;
    $totalSewaDanFeeSd = $sewaBulanan + $biayaFeeSdBulanIni;
    $mesin = $typeMesin[$i % count($typeMesin)];
    
    $sheet->setCellValue('A' . $row, $i + 1);
    $sheet->setCellValue('B' . $row, $tahun);
    $sheet->setCellValue('C' . $row, $bulan);
    $sheet->setCellValue('D' . $row, $unitKerjaList[$i]);
    $sheet->setCellValue('E' . $row, $costCentres[$i]);
    $sheet->setCellValue('F' . $row, $pemakaianBulanIni);
    $sheet->setCellValue('G' . $row, $pemakaianSdBulanIni);
    $sheet->setCellValue('H' . $row, '-');
    $sheet->setCellValue('I' . $row, $mesin);
    $sheet->setCellValue('J' . $row, $biayaFeeBulanIni);
    $sheet->setCellValue('K' . $row, $biayaFeeSdBulanIni);
    $sheet->setCellValue('L' . $row, $feePerLembar);
    $sheet->setCellValue('M' . $row, $sewaBulanan);
    $sheet->setCellValue('N' . $row, $totalSewaDanFeeBulanIni);
    $sheet->setCellValue('O' . $row, $totalSewaDanFeeSd);
}

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$outFile = 'referensi/fotocopy/Template_jasa-fotocopy_filled.xlsx';
$writer->save($outFile);
echo "File disimpan ke: $outFile\n";
echo "13 baris data berhasil diisi.\n";
