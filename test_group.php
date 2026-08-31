<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\PerizinanProsesList::orderBy('tahun', 'desc')
                                  ->orderBy('nama_proses', 'asc')
                                  ->orderBy('id', 'asc')
                                  ->skip(20)->take(10)->get();

echo "Raw items:\n";
foreach($items as $i) {
    echo $i->id . " | " . $i->tahun . " | " . $i->nama_proses . " | " . $i->target . "\n";
}

$grouped = collect($items)->groupBy(function ($item) {
    return $item->tahun . '_' . $item->nama_proses;
});

echo "\nGrouped:\n";
foreach($grouped as $key => $group) {
    echo "Group: $key (Count: " . $group->count() . ")\n";
}
