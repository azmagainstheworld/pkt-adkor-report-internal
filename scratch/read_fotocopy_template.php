<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'referensi/fotocopy/Template_jasa-fotocopy (4).xlsx';
$spreadsheet = IOFactory::load($file);

foreach ($spreadsheet->getSheetNames() as $i => $name) {
    echo "Sheet $i: $name\n";
    $sheet = $spreadsheet->getSheet($i);
    $highestRow = $sheet->getHighestRow();
    $highestCol = $sheet->getHighestColumn();
    echo "Rows: $highestRow, Cols: $highestCol\n";
    
    // Print first 5 rows
    for ($r = 1; $r <= min($highestRow, 5); $r++) {
        $rowData = [];
        for ($c = 'A'; $c <= $highestCol; $c++) {
            $val = $sheet->getCell($c . $r)->getValue();
            $rowData[] = $c . ': ' . $val;
        }
        echo "  Row $r: " . implode(' | ', $rowData) . "\n";
    }
    echo "\n";
}
