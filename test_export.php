<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PerizinanProsesExport;

try {
    Excel::store(new PerizinanProsesExport(true), 'test_template.xlsx', 'local');
    echo "Template generated\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
