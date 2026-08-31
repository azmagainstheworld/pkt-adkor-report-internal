<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "PaTekstualMaster count: " . App\Models\PaTekstualMaster::count() . "\n";
echo "PaNonTekstualType count: " . App\Models\PaNonTekstualType::count() . "\n";
