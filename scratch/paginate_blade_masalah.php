<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\masalah-kendala.blade.php';
$content = file_get_contents($file);

$lines = explode("\n", $content);
$newLines = [];
foreach ($lines as $line) {
    $newLines[] = $line;
    if (strpos($line, '</x-table>') !== false) {
        $newLines[] = '            <div class="mt-4 px-4 pb-4">';
        $newLines[] = '                {{ $dataMasalah->links() }}';
        $newLines[] = '            </div>';
    }
}
file_put_contents($file, implode("\n", $newLines));
echo "SUCCESS\n";
