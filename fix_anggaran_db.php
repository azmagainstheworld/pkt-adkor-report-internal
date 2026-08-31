<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$headers = ['Anggaran Dikelola', 'Anggaran Rutin', 'Anggaran Investasi', 'Total'];

$deleted = App\Models\AnggaranAdministrasi::whereIn('detail_anggaran', $headers)->delete();

echo "Deleted $deleted rows that were category headers.";

// Auto assign kategori based on detail_anggaran for existing data!
$kategoriMapping = [
    'Pemeliharaan - Peralatan Kantor' => 'Dikelola',
    'Cetak dan Fotocopy' => 'Dikelola',
    'Pos Materai dan Pengiriman Dok.' => 'Dikelola',
    'Iuran Keanggotaan' => 'Dikelola',
    'Inspeksi dan Perijinan' => 'Dikelola',
    'Sewa - Peralatan Pabrik & Kantor' => 'Dikelola',
    'Jasa - Konsultan' => 'Dikelola',
    
    'Rekreasi dan Olahraga' => 'Rutin',
    'Peralatan Kantor' => 'Rutin',
    'Biaya Makan Minum' => 'Rutin',
    'Perjalanan Dinas Dalam Negeri' => 'Rutin',
    
    'Perlengkapan & Peralatan (Alat-alat Kantor)' => 'Investasi',
    'Perlengkapan & Peralatan (Furniture Kantor)' => 'Investasi',
    'Aset Ttp dlm Proses Konstruksi-Bangunan&Prasarana (HGB)' => 'Investasi',
];

$updated = 0;
foreach(App\Models\AnggaranAdministrasi::all() as $row) {
    if(isset($kategoriMapping[$row->detail_anggaran])) {
        $row->kategori = $kategoriMapping[$row->detail_anggaran];
        $row->save();
        $updated++;
    }
}
echo "\nUpdated $updated rows with correct kategori!";
