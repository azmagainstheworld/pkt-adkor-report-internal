<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$row = collect(['nomor_laporan' => '123']);
echo isset($row['nomor_laporan']) ? "YES\n" : "NO\n";
echo array_key_exists('nomor_laporan', $row) ? "YES_KEY\n" : "NO_KEY\n";
