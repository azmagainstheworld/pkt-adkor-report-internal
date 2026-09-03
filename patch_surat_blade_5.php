<?php
$filePath = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($filePath);

// 1. Update Dropdown texts
$content = str_replace('Tabel 1 Saja (Rekap)', 'Tabel Rekapitulasi', $content);
$content = str_replace('Tabel 2 Saja (Detail)', 'Tabel Rincian', $content);
$content = str_replace('Tabel 1 & 2 (Semua)', 'Tabel Lengkap', $content);

// 2. Update form action URL for edit modal
$content = str_replace('document.getElementById(\'formEditDataSatuan\').action = "/surat/" + row.id;', 'document.getElementById(\'formEditDataSatuan\').action = "/administrasi/surat-masuk-keluar/" + row.id;', $content);

file_put_contents($filePath, $content);
echo "Texts and form action patched.\n";
