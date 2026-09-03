<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DummyImport implements ToModel, WithHeadingRow {
    public function model(array $row) {
        print_r($row);
        return null;
    }
}

file_put_contents('test_import.csv', "Tahun,Bulan,Undangan Intern,Undangan Ekstern\n2026,Januari,10,20\n");

try {
    Excel::import(new DummyImport, 'test_import.csv');
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
