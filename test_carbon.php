<?php
require 'vendor/autoload.php';
use Carbon\Carbon;

$dates = [
    '12-Jan-24',
    '12-Feb-24',
    '12-Mar-24',
    '12-Apr-24',
    '12-Mei-24',
    '12-Jun-24',
    '12-Jul-24',
    '12-Agustus-24',
    '12-Sep-24',
    '12-Okt-24',
    '12-Nov-24',
    '12-Des-24'
];

$bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
$bulanEng =  ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

foreach ($dates as $d) {
    try {
        $replaced = str_ireplace($bulanIndo, $bulanEng, $d);
        // Sometimes Carbon struggles with 2 digit years if the format is ambiguous. Let's see.
        // Or if it's '12-May-24' it's fine.
        $parsed = Carbon::parse($replaced)->format('Y-m-d');
        echo "$d -> $replaced -> $parsed\n";
    } catch (\Exception $e) {
        echo "$d -> ERROR: " . $e->getMessage() . "\n";
    }
}
