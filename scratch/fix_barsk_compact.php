<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\BarSkMemoController.php';
$content = file_get_contents($file);

// Add 'dataTerbit', 'dataProses' to compact
$content = str_replace("'rawData', 'grandTotalTerbit'", "'rawData', 'dataTerbit', 'dataProses', 'grandTotalTerbit'", $content);

file_put_contents($file, $content);
echo "Fixed compact";
