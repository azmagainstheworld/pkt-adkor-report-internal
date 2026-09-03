<?php

$file = 'resources/views/pemeliharaan.blade.php';
$content = file_get_contents($file);

// 1. Rename table
$content = str_replace(
    'TABEL PEMELIHARAAN RUTIN (NO 1)',
    'PEMELIHARAAN PERALATAN KANTOR & FURNITUR KANTOR',
    $content
);

// Rename "Mode Hapus Massal" to "Hapus semua"
$content = str_replace(
    'Mode Hapus Massal',
    'Hapus semua',
    $content
);

// 2. Remove Tabel 2 (Peralatan)
// Look for '<!-- ================= TABEL 2: PERALATAN ================= -->'
// up to '<!-- MODAL HAPUS KONFIRMASI -->'
$pattern = '/<!-- ================= TABEL 2: PERALATAN ================= -->.*?<!-- MODAL HAPUS KONFIRMASI -->/s';
$content = preg_replace($pattern, '<!-- MODAL HAPUS KONFIRMASI -->', $content);

// Remove modals for Peralatan
$pattern = '/<!-- Modal Atur Kolom Peralatan -->.*?<!-- ================= IMPORT EXCEL MODALS ================= -->/s';
$content = preg_replace($pattern, '<!-- ================= IMPORT EXCEL MODALS ================= -->', $content);

$pattern = '/<x-import-modal id="modalImportPeralatan" .*?\/>/s';
$content = preg_replace($pattern, '', $content);

$pattern = '/<x-delete-modal id="modalHapusPeralatan" .*?\/>/s';
$content = preg_replace($pattern, '', $content);

$pattern = '/<!-- ================= MODAL ATUR DOKUMEN \(PERALATAN\) ================= -->.*?<!-- Modal Atur Kolom Rutin -->/s';
$content = preg_replace($pattern, '<!-- Modal Atur Kolom Rutin -->', $content);

$pattern = '/<x-modal id="modalEditPeralatan".*?<\/x-modal>/s';
$content = preg_replace($pattern, '', $content);

file_put_contents($file, $content);
echo "Blade updated.\n";
