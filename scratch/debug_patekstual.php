<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$masters = App\Models\PaTekstualMaster::all();
echo "Masters in DB:\n";
foreach($masters as $m) {
    echo "ID: {$m->id}, Kel: {$m->kelompok_tabel}, Nama: '{$m->nama_dokumen}'\n";
}

$file1 = 'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\non teknik\Nonteknik_tabel1.xlsx';
if(file_exists($file1)) {
    echo "\nHeaders in Excel 1:\n";
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file1);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    $headers = $rows[0];
    foreach($headers as $h) {
        if(!empty($h)) {
            echo "'" . trim($h) . "'\n";
        }
    }
}
