<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = App\Models\PaTekstualData::take(10)->get();
foreach($data as $d) {
    echo "tahun: {$d->tahun}, bulan: {$d->bulan}, master_id: {$d->master_id}, jumlah: {$d->jumlah}\n";
}
