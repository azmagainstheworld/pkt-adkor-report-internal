<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/kendala.xlsx';
$rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\MasalahKendalaImport, $file);
print_r(array_keys($rows[0][0]));
