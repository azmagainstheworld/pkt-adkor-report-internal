<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pelaporan;
use Illuminate\Support\Collection;

$import = new \App\Imports\PelaporanImport();
$rows = collect([
    collect([
        'nomor' => 'TEST-002',
        'tujuan' => 'Eksternal',
        'laporan' => 'Laporan Test 2',
        'tanggal' => '2026-08-30',
        'jenis' => 'Bulan',
    ])
]);
$import->collection($rows);
echo "Count after import: " . Pelaporan::where('nomor', 'TEST-002')->count() . "\n";
