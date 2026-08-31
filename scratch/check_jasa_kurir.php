<?php
require 'vendor/autoload.php';
$file = 'D:\web_pkt_adkor_internal\adkor-report-internal\referensi\Jasa kurir.xlsx';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();
print_r(array_slice($rows, 0, 5));
