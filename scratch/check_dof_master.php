<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$m = App\Models\DofMaster::all();
foreach($m as $x) {
    echo $x->id . ' | ' . $x->kelompok_tabel . ' | ' . $x->nama_kegiatan . "\n";
}
