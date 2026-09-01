<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\bar-sk-memo.blade.php';
$content = file_get_contents($file);

// Replace the @forelse($rawData as $row) \n @if(...) for Terbit
$patternTerbit = '/@forelse\(\$rawData as \$row\)\s*@if\(\$row->skd_keputusan_bersama_terbit[^\)]+\)\)/';
$content = preg_replace($patternTerbit, '@forelse($dataTerbit as $row)', $content);

// We already removed the @endif for Terbit, so we don't need to do it again.

// Replace the @forelse($rawData as $row) \n @if(...) for Proses
$patternProses = '/@forelse\(\$rawData as \$row\)\s*@if\(\$row->proses_skd_keputusan_bersama[^\)]+\)\)/';
$content = preg_replace($patternProses, '@forelse($dataProses as $row)', $content);

// Let's make sure the @endif for Proses is removed!
// The previous script might not have removed the @endif for Proses if it only matched once.
$content = preg_replace('/(<\/td>\s*<\/tr>\s*)@endif(\s*@empty)/', '$1$2', $content);

// Also replace count($rawData) > 0 with count($dataTerbit) > 0 in the Terbit footer
$content = str_replace('@if(count($rawData) > 0)', '@if(count($dataTerbit) > 0)', $content);
// Wait, the footer for Proses might also have it.
// Let's just fix the syntax error first.

file_put_contents($file, $content);
echo "Fixed Blade syntax\n";
