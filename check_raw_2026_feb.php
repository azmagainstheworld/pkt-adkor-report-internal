<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$array = \Maatwebsite\Excel\Facades\Excel::toArray(new App\Imports\AnggaranImport, 'referensi/Template_anggaran.xlsx');

foreach($array[0] as $idx => $row) {
    if (isset($row['tahun']) && $row['tahun'] === '2026 FEBRUARI') {
        echo "Found raw: " . json_encode($row) . "\n";
    }
}
