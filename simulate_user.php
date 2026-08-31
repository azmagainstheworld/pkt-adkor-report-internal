<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PerizinanProsesImport;

$filePath = __DIR__ . '/referensi/Template_perizinan-proses.xlsx';

if (!file_exists($filePath)) {
    echo "File not found: $filePath\n";
    exit;
}

$file = new UploadedFile($filePath, 'Template_perizinan-proses.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

try {
    Excel::import(new PerizinanProsesImport, $file);
    echo "Import successful.\n";
    
    $data = \App\Models\PerizinanProsesList::all();
    echo "Count in DB: " . count($data) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
