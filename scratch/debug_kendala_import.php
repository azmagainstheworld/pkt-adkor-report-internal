<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/kendala.xlsx';
$rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\MasalahKendalaImport, $file);

echo "Total sheets: " . count($rows) . "\n";
echo "Total rows in sheet 0: " . count($rows[0]) . "\n";
foreach($rows[0] as $index => $row) {
    if ($index < 5) {
        print_r($row);
        echo "Check key masalahkendala: " . (isset($row['masalahkendala']) ? 'YES' : 'NO') . "\n";
        echo "Trimmed: '" . trim($row['masalahkendala'] ?? '') . "'\n";
    }
}

// Try running the import directly via facade to see if it throws any DB errors
try {
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\MasalahKendalaImport, $file);
    echo "\nImport executed successfully via facade.\n";
} catch (\Exception $e) {
    echo "\nImport FAILED: " . $e->getMessage() . "\n";
}

$count = \App\Models\MasalahKendala::count();
echo "Total MasalahKendala in DB: " . $count . "\n";
