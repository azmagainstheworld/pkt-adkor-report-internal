<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PerizinanProsesImport;

$file = new UploadedFile(__DIR__ . '/referensi/Template_perizinan-proses_NEW.xlsx', 'Template_perizinan-proses_NEW.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

try {
    Excel::import(new PerizinanProsesImport, $file);
    echo "Import successful.\n";
    
    $data = \App\Models\PerizinanProsesList::all();
    echo "Count in DB: " . count($data) . "\n";
    print_r($data->toArray());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
