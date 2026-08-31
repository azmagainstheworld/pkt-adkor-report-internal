<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \App\Models\PerizinanProsesList::all();
echo "Total proses: " . $rows->count() . "\n";
foreach ($rows as $row) {
    echo "ID: $row->id | Tahun: $row->tahun | Nama: $row->nama_proses | Target: $row->target | Periode: $row->periode\n";
}
