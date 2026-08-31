<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PerizinanTerbit;
use App\Models\PerizinanProsesList;
use Illuminate\Support\Facades\DB;

echo "PerizinanTerbit count: " . PerizinanTerbit::count() . "\n";
echo "PerizinanProsesList count: " . PerizinanProsesList::count() . "\n";

$latestTerbit = PerizinanTerbit::latest('id')->first();
$latestProses = PerizinanProsesList::latest('id')->first();

echo "Latest Terbit: " . ($latestTerbit ? json_encode($latestTerbit->toArray()) : 'None') . "\n";
echo "Latest Proses: " . ($latestProses ? json_encode($latestProses->toArray()) : 'None') . "\n";
