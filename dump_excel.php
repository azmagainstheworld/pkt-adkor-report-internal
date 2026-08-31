<?php
require __DIR__ . '/vendor/autoload.php';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile('referensi/Template_perizinan-proses.xlsx');
$spreadsheet = $reader->load('referensi/Template_perizinan-proses.xlsx');

foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
    echo "=== SHEET: $sheetName ===\n";
    $sheet = $spreadsheet->getSheet($sheetIndex);
    $data = $sheet->toArray();
    echo "Total rows: " . count($data) . "\n";
    foreach ($data as $i => $row) {
        $nonEmpty = array_filter($row, function($v) { return $v !== null && $v !== ''; });
        if (!empty($nonEmpty)) {
            echo "Row $i: " . json_encode($nonEmpty) . "\n";
        }
    }
}
