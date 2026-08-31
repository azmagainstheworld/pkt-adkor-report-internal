<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/non teknik/NON teknik Non tekstual.xlsx';
$import = new App\Imports\PaNonTekstualImport;
Maatwebsite\Excel\Facades\Excel::import($import, $file);
echo App\Models\PaNonTekstualValue::count() . "\n";
