<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\bar-sk-memo.blade.php';
$content = file_get_contents($file);

// Fix the syntax error by using double quotes for the HTML string so we can use single quotes inside
$searchTerbitError = "['<input type=\"checkbox\" id=\"selectAllTerbit\" onclick=\"toggleSelectAll('terbit')\">', 'Tahun'";
$replaceTerbitFix = '["<input type=\\"checkbox\\" id=\\"selectAllTerbit\\" onclick=\\"toggleSelectAll(\'terbit\')\\">", \'Tahun\'';
$content = str_replace($searchTerbitError, $replaceTerbitFix, $content);

$searchProsesError = "['<input type=\"checkbox\" id=\"selectAllProses\" onclick=\"toggleSelectAll('proses')\">', 'Tahun'";
$replaceProsesFix = '["<input type=\\"checkbox\\" id=\\"selectAllProses\\" onclick=\\"toggleSelectAll(\'proses\')\\">", \'Tahun\'';
$content = str_replace($searchProsesError, $replaceProsesFix, $content);

file_put_contents($file, $content);
echo "Syntax error fixed";
