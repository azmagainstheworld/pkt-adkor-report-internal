<?php
require 'vendor/autoload.php';

use App\Imports\PengirimanDokumenImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;

// We will dump the $row keys using a small modification to the class or mock class.
// Since we can't easily mock, let's just make a small script to import and dump.
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class DumpImport implements \Maatwebsite\Excel\Concerns\ToCollection, \Maatwebsite\Excel\Concerns\WithHeadingRow
{
    public function collection(\Illuminate\Support\Collection $rows)
    {
        foreach ($rows as $row) {
            print_r($row->toArray());
            break;
        }
    }
}

Excel::import(new DumpImport, 'd:/web_pkt_adkor_internal/adkor-report-internal/referensi/Template_Pengiriman_Biaya_Ongkir.xlsx');
