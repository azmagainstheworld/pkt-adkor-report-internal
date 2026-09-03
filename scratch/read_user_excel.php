<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;

$file = 'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\Surat\Template_Import_Undangan (2).xlsx';

$array = Excel::toArray(new class implements Maatwebsite\Excel\Concerns\ToCollection {
    public function collection(\Illuminate\Support\Collection $rows) {}
}, $file);

// Get the first row of the first sheet
echo "Headers in row 1:\n";
echo json_encode($array[0][0]) . "\n";

echo "Headers in row 2:\n";
echo json_encode($array[0][1]) . "\n";
