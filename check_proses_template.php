<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_perizinan-proses.xlsx';
$rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\PerizinanProsesImport, $file);
print_r($rows[0][0]);
