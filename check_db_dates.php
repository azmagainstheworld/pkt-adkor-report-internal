<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$rows = \App\Models\PerizinanTerbit::take(5)->get();
foreach ($rows as $r) {
    echo $r->id . ' | ' . $r->tanggal_sejak . "\n";
}
