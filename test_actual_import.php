<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PemeliharaanRutinImport;
use App\Models\PemeliharaanRutinData;

$filePath = 'C:\Users\LENOVO\Downloads\Template_pemeliharaan-rutin (1).xlsx';
echo "Running actual import on user's file...\n";

try {
    Excel::import(new PemeliharaanRutinImport, $filePath);
    echo "Import completed!\n";
    $count = PemeliharaanRutinData::count();
    echo "Total records in pemeliharaan_rutin_data now: $count\n";
} catch (\Exception $e) {
    echo "Import failed with error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
