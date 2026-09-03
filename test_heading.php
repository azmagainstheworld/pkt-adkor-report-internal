<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use App\Models\PemeliharaanRutinMaster;
use Illuminate\Support\Str;

$masters = PemeliharaanRutinMaster::all();

echo "Default HeadingRowFormatter behavior for masters:\n";
foreach($masters as $master) {
    $formatted = HeadingRowFormatter::format([$master->nama_pemeliharaan]);
    $slugged = Str::slug($master->nama_pemeliharaan, '_');
    echo "Original: {$master->nama_pemeliharaan}\n";
    echo "Formatter: {$formatted[0]}\n";
    echo "Str::slug: {$slugged}\n\n";
}
