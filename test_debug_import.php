<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = 'storage/app/public/Template_Import_Pelaporan.xlsx';
if (!file_exists($file)) {
    // Generate one
    \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\PelaporanExport(true), 'public/Template_Import_Pelaporan.xlsx');
}
\Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\DebugImport, $file);
