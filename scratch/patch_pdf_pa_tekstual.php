<?php
$file = 'resources/views/pdf/pa-tekstual.blade.php';
$content = file_get_contents($file);
$content = str_replace('nama_kegiatan', 'nama_dokumen', $content);
file_put_contents($file, $content);
echo "PDF view patched.\n";
