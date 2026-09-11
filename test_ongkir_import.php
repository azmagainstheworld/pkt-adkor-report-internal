<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PengirimanDokumenImport('ongkir'), 'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\pengiriman_dokumen\Template_Pengiriman_Biaya_Ongkir.xlsx');
    echo 'SUCCESS: Import completed. DB rows: ' . \App\Models\PengirimanOngkir::count();
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
}
