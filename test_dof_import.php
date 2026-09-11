<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\DofMaster;
use App\Models\DofData;

// Let's create a test excel file that mimics what the user uploads
$master1 = DofMaster::where('kelompok_tabel', 1)->get();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Tahun');
$sheet->setCellValue('B1', 'Bulan');

$colIndex = 3;
foreach ($master1 as $m) {
    $sheet->setCellValueByColumnAndRow($colIndex, 1, $m->nama_kegiatan);
    $colIndex++;
}

// Data row
$sheet->setCellValue('A2', '2026');
$sheet->setCellValue('B2', 'Juli');
$colIndex = 3;
foreach ($master1 as $m) {
    $sheet->setCellValueByColumnAndRow($colIndex, 2, '5'); // 5 uses
    $colIndex++;
}

$writer = new Xlsx($spreadsheet);
$filePath = __DIR__.'/test_dof_import.xlsx';
$writer->save($filePath);

echo "Importing...\n";
try {
    Excel::import(new \App\Imports\DofImport(1, 'test-uuid'), $filePath);
    echo "Import finished.\n";
    echo "DofData count: " . DofData::count() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
