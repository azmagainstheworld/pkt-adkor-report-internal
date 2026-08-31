<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \App\Models\PerizinanProsesList::orderBy('id', 'asc')->get();
$seen = [];
$deleted = 0;

foreach ($rows as $row) {
    $hash = md5($row->tahun . '_' . $row->nama_proses . '_' . $row->target . '_' . $row->periode);
    if (isset($seen[$hash])) {
        $row->delete();
        $deleted++;
    } else {
        $seen[$hash] = true;
    }
}

echo "Deleted $deleted exact duplicate records in PerizinanProsesList.\n";
