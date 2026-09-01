<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\bar-sk-memo.blade.php';
$content = file_get_contents($file);

// We need to split colspan="3" into an empty td and colspan="2"
$search1 = '<td colspan="3" class="px-4 py-3 text-right uppercase border-r border-gray-300">Total Keseluruhan</td>';
$replace1 = '<td></td><td colspan="2" class="px-4 py-3 text-right uppercase border-r border-gray-300">Total Keseluruhan</td>';

$content = str_replace($search1, $replace1, $content);

file_put_contents($file, $content);
echo "Totals row colspan fixed.\n";
