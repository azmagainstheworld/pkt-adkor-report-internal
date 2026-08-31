<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ErrorImport, 'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\Template_program-strategis.xlsx');
    echo "NO EXCEPTION CAUGHT\n";
} catch (\Exception $e) {
    echo "CAUGHT EXCEPTION\n";
} catch (\Throwable $e) {
    echo "CAUGHT THROWABLE: " . get_class($e) . "\n";
}
