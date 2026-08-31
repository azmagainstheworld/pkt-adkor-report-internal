<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DofMaster;
use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

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

$allNames = array_merge($tabel1Names, $tabel2Names);

// Instead of updating existing, let's update their IDs to force the order
$id = 1;
foreach ($allNames as $name) {
    $master = DofMaster::where('nama_kegiatan', $name)->first();
    if ($master) {
        // If ID is different, update it
        if ($master->id != $id) {
            DB::table('dof_masters')->where('id', $master->id)->update(['id' => 9999 + $id]); // Temp
        }
    }
    $id++;
}

$id = 1;
foreach ($allNames as $name) {
    DB::table('dof_masters')->where('nama_kegiatan', $name)->update(['id' => $id]);
    $id++;
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$masters = DofMaster::orderBy('id', 'asc')->get();
foreach($masters as $m) {
    echo $m->id . " - " . $m->nama_kegiatan . " (Tabel " . $m->kelompok_tabel . ")\n";
}
