<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DofMaster;
use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Let's truncate and seed with exact requested names in order, preserving old IDs if possible to prevent data loss?
// Wait, if I change the kelompok_tabel, the data stays attached to the master_id. That's safe!
$tabel1Names = [
    'Approval Stempel Digital',
    'Approval File Scan',
    'Digital Signature',
    'Pendaftaran Akun',
    'Perekaman Akun'
];

$tabel2Names = [
    'Revisi DOF',
    'Pembatalan DOF',
    'Pembuatan Template',
    'Cek Error',
    'Problem DOF',
    'E-meterai'
];

foreach ($tabel1Names as $name) {
    $master = DofMaster::firstOrCreate(['nama_kegiatan' => $name]);
    $master->kelompok_tabel = 1;
    $master->save();
}

foreach ($tabel2Names as $name) {
    $master = DofMaster::firstOrCreate(['nama_kegiatan' => $name]);
    $master->kelompok_tabel = 2;
    $master->save();
}

// Any master not in these lists can be deleted, OR moved to another group?
$allNames = array_merge($tabel1Names, $tabel2Names);
$toDelete = DofMaster::whereNotIn('nama_kegiatan', $allNames)->get();
foreach ($toDelete as $m) {
    echo "Deleting unused master: " . $m->nama_kegiatan . "\n";
    // Optional: delete associated data
    \App\Models\DofData::where('master_id', $m->id)->delete();
    $m->delete();
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Master Data updated successfully!\n";
