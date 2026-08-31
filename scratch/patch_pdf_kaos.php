<?php

$pdfFile = 'resources/views/pdf/karyawan.blade.php';
$pdfContent = file_get_contents($pdfFile);

// Hapus baris dengan aman
$pdfContent = preg_replace('/<th>Ukuran Kaos<\/th>\s*/i', '', $pdfContent);
$pdfContent = preg_replace('/<td class="text-center">{{ \$item->ukuran_kaos }}<\/td>\s*/i', '', $pdfContent);

file_put_contents($pdfFile, $pdfContent);

echo "Ukuran Kaos removed safely from PDF view.\n";
