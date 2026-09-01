<?php
require 'vendor/autoload.php';
$file = 'D:/web_pkt_adkor_internal/adkor-report-internal/referensi/kendala.xlsx';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($file);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();
print_r($rows[0]);
