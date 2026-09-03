<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'd:/web_pkt_adkor_internal/adkor-report-internal/referensi/Template_Pengiriman_Biaya_Ongkir.xlsx';
if (!file_exists($filePath)) {
    die("File not found: " . $filePath);
}
$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();
$highestRow = $worksheet->getHighestRow();

echo "Highest Row: " . $highestRow . "\n";
