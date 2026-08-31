<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$types = App\Models\PaNonTekstualType::all();
foreach($types as $t) {
    $t->name = str_replace('Tesktual', 'Tekstual', $t->name);
    $t->save();
}
echo "DB updated.\n";
