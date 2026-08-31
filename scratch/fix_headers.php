<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PaNonTekstualType;
use App\Models\PaTekstualMaster;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

echo "Memperbaiki Typo di PA Non Tekstual...\n";
$type = PaNonTekstualType::where('name', 'Rekap Sidovit (Non Tekstual)')->first();
if ($type) {
    $type->name = 'Rekap Sidovit (Non Tesktual)';
    $type->save();
    echo "- Typo berhasil diperbaiki.\n";
}

echo "\nMenyiapkan Kolom untuk PA Tekstual (Tabel 1)...\n";
$file1 = 'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\non teknik\Nonteknik_tabel1.xlsx';
if(file_exists($file1)) {
    $spreadsheet = IOFactory::load($file1);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    $headers = $rows[0];
    
    // Clear existing for group 1 if any just in case
    PaTekstualMaster::where('kelompok_tabel', 1)->delete();

    for($i = 2; $i < count($headers); $i++) {
        $h = trim($headers[$i]);
        if(!empty($h)) {
            PaTekstualMaster::create([
                'kelompok_tabel' => 1,
                'nama_dokumen' => $h,
                'is_active' => true
            ]);
            echo "- Ditambahkan ke Tabel 1: $h\n";
        }
    }
}

echo "\nMenyiapkan Kolom untuk PA Tekstual (Tabel 2)...\n";
$file2 = 'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\non teknik\Nonteknik_tabel2.xlsx';
if(file_exists($file2)) {
    $spreadsheet = IOFactory::load($file2);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    $headers = $rows[0];
    
    // Clear existing for group 2
    PaTekstualMaster::where('kelompok_tabel', 2)->delete();

    for($i = 2; $i < count($headers); $i++) {
        $h = trim($headers[$i]);
        if(!empty($h)) {
            PaTekstualMaster::create([
                'kelompok_tabel' => 2,
                'nama_dokumen' => $h,
                'is_active' => true
            ]);
            echo "- Ditambahkan ke Tabel 2: $h\n";
        }
    }
}
echo "Selesai.\n";
