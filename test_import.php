<?php
require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Imports\PengirimanDokumenImport;
use Maatwebsite\Excel\Facades\Excel;

try {
    Excel::import(new PengirimanDokumenImport('ongkir'), 'd:/web_pkt_adkor_internal/adkor-report-internal/referensi/Template_Pengiriman_Biaya_Ongkir_Filled.xlsx');
    echo "Import successful.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
