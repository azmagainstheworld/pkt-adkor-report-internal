<?php

$file = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$content = file_get_contents($file);

// 1. Rename "Mode Hapus Massal" to "Hapus Semua"
$content = str_replace('Mode Hapus Massal', 'Hapus Semua', $content);

// 2. Rename "Tambah Data Tabel 1" and "Tambah Data Tabel 2" to "Tambah Data"
$content = str_replace('Tambah Data Tabel 1', 'Tambah Data', $content);
$content = str_replace('Tambah Data Tabel 2', 'Tambah Data', $content);

// 3. Fix nama_kegiatan to nama_dokumen in loops and JS
$content = str_replace('$master->nama_kegiatan', '$master->nama_dokumen', $content);
$content = str_replace('${m.nama_kegiatan}', '${m.nama_dokumen}', $content);

// 4. Update the forms to remove onsubmit confirm and add hidden `delete_all` flag
$form1_old = '<form id="bulkDeleteForm1" action="{{ route(\'pa-tekstual.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih pada Tabel 1?\')">';
$form1_new = '<form id="bulkDeleteForm1" action="{{ route(\'pa-tekstual.destroyBulk\') }}" method="POST">
                <input type="hidden" name="delete_all" id="deleteAll1" value="0">
                <input type="hidden" name="filter_tahun" value="{{ request(\'tahun\', \'semua\') }}">
                <input type="hidden" name="filter_bulan" value="{{ request(\'bulan\', \'semua\') }}">
                <input type="hidden" name="kelompok_tabel" value="1">';
$content = str_replace($form1_old, $form1_new, $content);

$form2_old = '<form id="bulkDeleteForm2" action="{{ route(\'pa-tekstual.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih pada Tabel 2?\')">';
$form2_new = '<form id="bulkDeleteForm2" action="{{ route(\'pa-tekstual.destroyBulk\') }}" method="POST">
                <input type="hidden" name="delete_all" id="deleteAll2" value="0">
                <input type="hidden" name="filter_tahun" value="{{ request(\'tahun\', \'semua\') }}">
                <input type="hidden" name="filter_bulan" value="{{ request(\'bulan\', \'semua\') }}">
                <input type="hidden" name="kelompok_tabel" value="2">';
$content = str_replace($form2_old, $form2_new, $content);

// 5. Hide checkboxes initially by adding a class `hidden` to the table headers and cells
// In kearsipan-pa-non-teknik-tekstual.blade.php, the checkboxes are hardcoded:
// $headTabel1 = ['<input type="checkbox" id="selectAllBulk1" onclick="toggleSelectAll1()">', 'Tahun', 'Bulan'];
// We change it to:
// $headTabel1 = ['<input type="checkbox" id="selectAllBulk1" class="bulk-cb-header-1 hidden" onclick="toggleSelectAll1()">', 'Tahun', 'Bulan'];
$content = str_replace(
    'id="selectAllBulk1" onclick="toggleSelectAll1()"',
    'id="selectAllBulk1" class="bulk-cb-header-1 hidden" onclick="toggleSelectAll1()"',
    $content
);
$content = str_replace(
    'id="selectAllBulk2" onclick="toggleSelectAll2()"',
    'id="selectAllBulk2" class="bulk-cb-header-2 hidden" onclick="toggleSelectAll2()"',
    $content
);

// Hide the table cell checkboxes initially
$content = str_replace(
    'class="cb-bulk-1"',
    'class="cb-bulk-1 hidden"',
    $content
);
$content = str_replace(
    'class="cb-bulk-2"',
    'class="cb-bulk-2 hidden"',
    $content
);


file_put_contents($file, $content);
echo "Blade view patched.\n";
