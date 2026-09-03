<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;

class TestImport implements \Maatwebsite\Excel\Concerns\ToArray {
    public function array(array $array) {}
}

$file = 'd:/web_pkt_adkor_internal/adkor-report-internal/referensi/non teknik/NON teknik Non tekstual.xlsx';

try {
    $data = Excel::toArray(new TestImport, $file);
    echo "--- SHEET 1 DATA ---\n";
    foreach($data[0] as $i => $row) {
        if ($i > 5) break;
        echo "Row $i: " . json_encode($row) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
