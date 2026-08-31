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
        'nomor_laporan' => 'TEST-001',
        'tujuan_laporan' => 'Eksternal',
        'laporan' => 'Laporan Test',
        'tanggal' => '2026-08-30',
        'jenis' => 'Bulan',
    ])
]);
$import->collection($rows);
echo "Count after import: " . Pelaporan::where('nomor', 'TEST-001')->count() . "\n";
