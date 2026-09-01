<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\masalah-kendala.blade.php';
$content = file_get_contents($file);

// Replace name="masalah" with name="masalah_kendala"
$content = str_replace('name="masalah"', 'name="masalah_kendala"', $content);

// Replace row.masalah with row.masalah_kendala in Javascript
$content = str_replace('row.masalah;', 'row.masalah_kendala;', $content);

// Also need to fix the Blade display in the table:
$content = str_replace('{{ $row->masalah }}', '{{ $row->masalah_kendala }}', $content);

file_put_contents($file, $content);
echo "Blade template patched";
