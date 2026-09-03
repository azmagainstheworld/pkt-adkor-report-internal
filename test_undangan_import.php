<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UndanganImport;

file_put_contents('test_import.csv', "Tahun,Bulan,Undangan Intern,Undangan Ekstern\n2026,Januari,10,20\n");

try {
    Excel::import(new UndanganImport, 'test_import.csv');
    echo "Import processed.\n";
    
    $records = \App\Models\Undangan::all();
    echo "Records in DB:\n";
    foreach ($records as $r) {
        echo "- {$r->tahun} {$r->bulan} Intern: {$r->undangan_intern} Ekstern: {$r->undangan_ekstern}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
