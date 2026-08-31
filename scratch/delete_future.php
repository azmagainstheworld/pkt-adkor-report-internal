<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\PaTekstualData::where('tahun', '>', 2026)->delete();
App\Models\PaNonTekstualValue::where('tahun', '>', 2026)->delete();
echo "Deleted future dummy data.\n";
