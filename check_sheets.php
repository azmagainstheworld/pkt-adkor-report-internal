<?php
require __DIR__ . '/vendor/autoload.php';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile('referensi/Template_perizinan-proses.xlsx');
$spreadsheet = $reader->load('referensi/Template_perizinan-proses.xlsx');
foreach ($spreadsheet->getSheetNames() as $name) {
    echo "Sheet: $name\n";
}
