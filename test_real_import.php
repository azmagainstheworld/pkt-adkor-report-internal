<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class TestImport implements ToArray, WithHeadingRow
{
    public function array(array $array)
    {
        echo "Headers parsed by Laravel Excel:\n";
        if (count($array) > 0) {
            foreach (array_keys($array[0]) as $key) {
                echo "- " . $key . "\n";
            }
        }
    }
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Tahun');
$sheet->setCellValue('B1', 'Bulan');
$sheet->setCellValue('C1', "Perbaikan & Pengecatan Roll O'pack");
$sheet->setCellValue('D1', "Perbaikan Mesin Laminating");

$sheet->setCellValue('A2', '2026');
$sheet->setCellValue('B2', 'September');
$sheet->setCellValue('C2', '5');
$sheet->setCellValue('D2', '2');

$writer = new Xlsx($spreadsheet);
$filePath = __DIR__.'/scratch/test_import.xlsx';
$writer->save($filePath);

echo "Importing...\n";
Excel::import(new TestImport, $filePath);

echo "\nComparing with Str::slug:\n";
echo "C1: " . Str::slug("Perbaikan & Pengecatan Roll O'pack", '_') . "\n";
echo "D1: " . Str::slug("Perbaikan Mesin Laminating", '_') . "\n";
