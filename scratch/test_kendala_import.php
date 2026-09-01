<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
\Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\MasalahKendalaImport, 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/kendala.xlsx');
echo 'Import Success!';
