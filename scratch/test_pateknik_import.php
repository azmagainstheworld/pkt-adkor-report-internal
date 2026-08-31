<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaTeknikImport;
use App\Models\PaTeknikData;

$file1 = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/Teknik/tabel 1.xlsx';
$file2 = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/Teknik/tabel 2.xlsx';

echo "Count before import: " . PaTeknikData::count() . "\n";

Excel::import(new PaTeknikImport('tabel1'), $file1);
echo "Count after tabel 1: " . PaTeknikData::count() . "\n";

Excel::import(new PaTeknikImport('tabel2'), $file2);
echo "Count after tabel 2: " . PaTeknikData::count() . "\n";
