<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/non teknik/NON teknik Non tekstual.xlsx';
if (!file_exists($file)) {
    echo "File not found\n";
    exit;
}
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
foreach($sheet->getRowIterator(1, 1) as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $headers = [];
    foreach($cellIterator as $cell) {
        $headers[] = $cell->getValue();
    }
    echo implode('|', $headers) . "\n";
}
