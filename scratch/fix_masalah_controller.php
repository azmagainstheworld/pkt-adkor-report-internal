<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\MasalahKendalaController.php';
$content = file_get_contents($file);

$content = str_replace("'masalah' => 'required|string',", "'masalah_kendala' => 'required|string',", $content);

// Also need to check if the UI sends 'masalah' or 'masalah_kendala'
file_put_contents($file, $content);
echo "Done";
