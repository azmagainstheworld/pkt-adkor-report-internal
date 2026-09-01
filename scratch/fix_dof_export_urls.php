<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\dof.blade.php';
$content = file_get_contents($file);

// Excel
$searchExcel = "route('dof.export.excel', ['tahun' => \$filterTahun, 'bulan' => \$filterBulan])";
$partsExcel = explode($searchExcel, $content);
if (count($partsExcel) === 3) {
    $content = $partsExcel[0] . "route('dof.export.excel', ['kelompok_tabel' => 1, 'tahun' => \$filterTahun, 'bulan' => \$filterBulan])" . $partsExcel[1] . "route('dof.export.excel', ['kelompok_tabel' => 2, 'tahun' => \$filterTahun, 'bulan' => \$filterBulan])" . $partsExcel[2];
}

// PDF
$searchPdf = "route('dof.export.pdf', ['tahun' => \$filterTahun, 'bulan' => \$filterBulan])";
$partsPdf = explode($searchPdf, $content);
if (count($partsPdf) === 3) {
    $content = $partsPdf[0] . "route('dof.export.pdf', ['kelompok_tabel' => 1, 'tahun' => \$filterTahun, 'bulan' => \$filterBulan])" . $partsPdf[1] . "route('dof.export.pdf', ['kelompok_tabel' => 2, 'tahun' => \$filterTahun, 'bulan' => \$filterBulan])" . $partsPdf[2];
}

file_put_contents($file, $content);
echo "OK\n";
