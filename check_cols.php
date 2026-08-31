<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
$cols = DB::table('dynamic_columns')->where('modul', 'perizinan_proses_list')->get();
echo "Dynamic Columns for perizinan_proses_list:\n";
foreach ($cols as $col) {
    echo $col->nama_kolom . "\n";
}
