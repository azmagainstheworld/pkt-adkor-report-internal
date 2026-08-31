<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\PerizinanProsesList::truncate();

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PerizinanProsesImport;

$filePath = __DIR__ . '/referensi/Template_perizinan-proses.xlsx';
$file = new UploadedFile($filePath, 'Template_perizinan-proses.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

try {
    Excel::import(new PerizinanProsesImport, $file);
    echo "Import successful.\n";
    $data = \App\Models\PerizinanProsesList::all();
    echo "Count in DB: " . count($data) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
