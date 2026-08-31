<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$tables = ['pa_tekstual_dokumen', 'pa_non_tekstual_master', 'pa_non_tekstual_dokumen'];
foreach($tables as $table) {
    if (Illuminate\Support\Facades\Schema::hasTable($table)) {
        echo "$table columns:\n";
        print_r(Illuminate\Support\Facades\Schema::getColumnListing($table));
        echo "\n";
    }
}
