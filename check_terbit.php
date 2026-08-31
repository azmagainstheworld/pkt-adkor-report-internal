<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$terbits = \App\Models\PerizinanTerbit::take(5)->get();
foreach($terbits as $t) {
    echo "ID: $t->id, Jenis ID: $t->jenis_perizinan_id, Nomor: $t->nomor\n";
    $jenis = \Illuminate\Support\Facades\DB::table('jenis_perizinan_master')->where('id', $t->jenis_perizinan_id)->first();
    echo "Jenis Name: " . ($jenis ? $jenis->nama_jenis : 'NULL') . "\n";
}
