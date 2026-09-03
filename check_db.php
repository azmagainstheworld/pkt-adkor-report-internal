<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = \App\Models\Undangan::all();
foreach($data as $d) {
    echo $d->tahun . ' ' . $d->bulan . ' Intern: ' . $d->undangan_intern . ' Ekstern: ' . $d->undangan_ekstern . "\n";
}
