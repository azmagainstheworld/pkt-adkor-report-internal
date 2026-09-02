<?php
$path = 'resources/views/jasakurir.blade.php';
$content = file_get_contents($path);

// The exact HTML from the file (using \s* to handle formatting differences)
$pattern = '/(<div class="[^"]*">[\s\r\n]*<span class="[^"]*">Konfigurasi<\/span>[\s\r\n]*<\/div>[\s\r\n]*<a href="javascript:void\(0\)" onclick="openModal\(\'modalAturKolom\'\)[^>]*>.*?Atur Kolom Tambahan[\s\r\n]*<\/a>)/is';

$content = preg_replace($pattern, "@if(auth()->user()->isAdmin())\n$1\n@endif", $content);

file_put_contents($path, $content);
echo "Atur Kolom fixed!\n";
?>
