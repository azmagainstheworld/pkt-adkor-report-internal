<?php

$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

$content = str_replace('Mode Hapus Massal', 'Hapus Semua', $content);

file_put_contents($file, $content);
echo "Button text patched.\n";
