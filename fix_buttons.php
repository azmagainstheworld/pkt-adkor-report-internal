<?php

$filePath = 'resources/views/undangan.blade.php';
$content = file_get_contents($filePath);

// 1. Remove "Atur Kolom Tambahan" from Rekapitulasi
// We look for dropdownUndangan
$patternRekap = '/<a href="javascript:void\(0\)" onclick="openModal\(\'modalAturKolom\'\); document\.getElementById\(\'dropdownUndangan\'\)\.classList\.add\(\'hidden\'\)".*?Atur Kolom Tambahan\s*<\/a>/s';
$content = preg_replace($patternRekap, '', $content);

// 2. Protect "Atur Kolom Tambahan" in Rincian (dropdownUndanganDetail)
$patternRincian = '/<a href="javascript:void\(0\)" onclick="openModal\(\'modalAturKolom\'\); document\.getElementById\(\'dropdownUndanganDetail\'\)\.classList\.add\(\'hidden\'\)".*?Atur Kolom Tambahan\s*<\/a>/s';
$content = preg_replace($patternRincian, "@if(auth()->check() && auth()->user()->isAdmin())\n$0\n@endif", $content);

file_put_contents($filePath, $content);
echo "Buttons updated.\n";
