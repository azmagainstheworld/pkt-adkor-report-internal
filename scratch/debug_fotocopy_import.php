<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'referensi/fotocopy/Template_jasa-fotocopy_import.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestRow();

echo "Total rows: $highestRow\n\n";

// Baca header row
$headers = [];
for ($c = 'A'; $c <= 'I'; $c++) {
    $headers[$c] = $sheet->getCell($c . '1')->getValue();
}
echo "Headers:\n";
print_r($headers);

echo "\n--- Sample Row 2 ---\n";
for ($c = 'A'; $c <= 'I'; $c++) {
    $val = $sheet->getCell($c . '2')->getValue();
    echo $headers[$c] . ": '$val'\n";
}

// Simulate what WithHeadingRow does (converts to slug/snake_case)
echo "\n--- Simulated key mapping (WithHeadingRow slug) ---\n";
foreach ($headers as $col => $header) {
    // Laravel Excel slug formatter: lowercase, replace spaces/special chars with underscore
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '_', trim($header)));
    $slug = trim($slug, '_');
    echo "$col: '$header' => key: '$slug'\n";
}

echo "\n--- What JasaFotocopyImport looks for ---\n";
echo "row['tahun'], row['bulan'], row['unit_kerja'], row['cost_centre'],\n";
echo "row['pemakaian_lbr'], row['fee_lbr'], row['sewa_bln']\n";
