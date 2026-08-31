<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\PaNonTekstualType;

$file = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/non teknik/NON teknik Non tekstual.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = [];
foreach($sheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $r = [];
    foreach($cellIterator as $cell) {
        $r[] = $cell->getCalculatedValue();
    }
    $rows[] = $r;
}

$types = PaNonTekstualType::all();
$typeMap = [];
foreach ($types as $t) {
    $typeMap[strtolower(trim($t->name))] = $t->id;
}
echo "Type Map:\n";
print_r($typeMap);

$headers = [];
$isFirstRow = true;
foreach ($rows as $row) {
    if ($isFirstRow) {
        $headers = $row;
        $isFirstRow = false;
        echo "Headers:\n";
        print_r($headers);
        continue;
    }
    
    $tahun = trim($row[0] ?? '');
    $bulan = trim($row[1] ?? '');
    if (empty($tahun) || empty($bulan)) continue;
    
    echo "Processing Tahun: $tahun, Bulan: $bulan\n";
    for ($i = 2; $i < count($headers); $i++) {
        $namaKolomExcel = trim($headers[$i] ?? '');
        if (empty($namaKolomExcel)) continue;

        $namaKolomLower = strtolower($namaKolomExcel);
        $namaKolomLower = str_replace('tesktual', 'tekstual', $namaKolomLower);

        echo "  Check kolom: '$namaKolomLower' -> ";
        if (isset($typeMap[$namaKolomLower])) {
            echo "MATCHED (ID: {$typeMap[$namaKolomLower]})\n";
        } else {
            echo "NOT MATCHED\n";
        }
    }
    break;
}
