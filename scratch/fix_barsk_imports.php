<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\BarSkMemoController.php';
$content = file_get_contents($file);

// Fix importExcelTerbit
$searchTerbit = "                \$data['proses_bar_monitoring'] = 0; \$data['proses_bar_manajemen'] = 0;";
$replaceTerbit = "                \$data['proses_bar_monitoring'] = 0; \$data['proses_bar_manajemen'] = 0;
                \$data['ba_terbit'] = 0; \$data['ba_proses'] = 0;";
$content = str_replace($searchTerbit, $replaceTerbit, $content);

// Fix importExcelProses
$searchProses = "                \$data['bar_monitoring_terbit'] = 0; \$data['bar_manajemen_terbit'] = 0;";
$replaceProses = "                \$data['bar_monitoring_terbit'] = 0; \$data['bar_manajemen_terbit'] = 0;
                \$data['ba_terbit'] = 0; \$data['ba_proses'] = 0;";
$content = str_replace($searchProses, $replaceProses, $content);

file_put_contents($file, $content);
echo "Fixed ba_terbit and ba_proses in imports";
