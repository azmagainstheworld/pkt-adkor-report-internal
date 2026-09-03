<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('pemeliharaan_rutin_data')->truncate();
DB::table('pemeliharaan_rutin_master')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$items = [
    "Perbaikan & Pengecatan Roll O'pack",
    "Perbaikan Dispenser",
    "Perbaikan Mesin Laminating",
    "Perbaikan furnitur",
    "Perbaikan infokus, LCD Proyektor, Video Tron, TV",
    "Perbaikan kunci",
    "Perbaikan mesin ketik",
    "Perbaikan mesin penghancur kertas",
    "Perbaikan microwave"
];

foreach ($items as $item) {
    DB::table('pemeliharaan_rutin_master')->insert(['nama_pemeliharaan' => $item]);
}

echo "Database updated successfully.\n";
