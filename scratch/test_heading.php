<?php

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use App\Models\PemeliharaanRutinMaster;

$masters = PemeliharaanRutinMaster::all();

echo "Default HeadingRowFormatter behavior for masters:\n";
foreach($masters as $master) {
    $formatted = HeadingRowFormatter::format($master->nama_pemeliharaan);
    $slugged = \Illuminate\Support\Str::slug($master->nama_pemeliharaan, '_');
    echo "Original: {$master->nama_pemeliharaan}\n";
    echo "Formatter: {$formatted}\n";
    echo "Str::slug: {$slugged}\n\n";
}
