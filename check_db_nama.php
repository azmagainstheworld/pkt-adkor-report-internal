<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \App\Models\PerizinanProsesList::where('nama_proses', 'like', 'Data Tanpa Nama%')->count();
echo "Data Tanpa Nama count: $count\n";

$all = \App\Models\PerizinanProsesList::select('id', 'nama_proses')->get();
foreach ($all as $item) {
    echo $item->id . ": " . $item->nama_proses . "\n";
}
