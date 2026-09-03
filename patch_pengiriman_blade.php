<?php

$filePath = 'resources/views/pengiriman-dokumen.blade.php';
$content = file_get_contents($filePath);

// 1. Move buttons to the right (Volume)
$content = preg_replace(
    '/<div class="mb-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center">/',
    '<div class="mb-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-end">',
    $content
);

// 2. Add hidden input tipe_tabel=volume
$content = str_replace(
    '<form id="bulkDeleteFormVolume" action="{{ route(\'pengiriman-dokumen.destroyBulk\') }}" method="POST">',
    '<form id="bulkDeleteFormVolume" action="{{ route(\'pengiriman-dokumen.destroyBulk\') }}" method="POST">' . "\n" . '                <input type="hidden" name="tipe_tabel" value="volume">',
    $content
);

// 3. Add hidden input tipe_tabel=ongkir
$content = str_replace(
    '<form id="bulkDeleteFormOngkir" action="{{ route(\'pengiriman-dokumen.destroyBulk\') }}" method="POST">',
    '<form id="bulkDeleteFormOngkir" action="{{ route(\'pengiriman-dokumen.destroyBulk\') }}" method="POST">' . "\n" . '                <input type="hidden" name="tipe_tabel" value="ongkir">',
    $content
);

// 4. Fix checkbox in Ongkir table
$badCheckbox = '<td class="px-6 py-4 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk-volume" value="{{ $record->id }}" onclick="toggleCheckboxVolume()"></td>';
$goodCheckbox = '<td class="px-6 py-4 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk-ongkir" value="{{ $cost->id }}" onclick="toggleCheckboxOngkir()"></td>';
$content = str_replace($badCheckbox, $goodCheckbox, $content);

// 5. Update delete single url to append ?tipe=volume or ?tipe=ongkir
// This one is tricky because both use `openDeleteModal('modalHapusLaporan', ...)`
// In volume table, it uses $record->id
$volumeSingleDeleteOld = <<<HTML
<button type="button" onclick="openDeleteModal('modalHapusLaporan', '{{ route('pengiriman-dokumen.destroy', \$record->id) }}')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Hapus Laporan Keseluruhan">
HTML;
$volumeSingleDeleteNew = <<<HTML
<button type="button" onclick="openDeleteModal('modalHapusLaporan', '{{ route('pengiriman-dokumen.destroy', ['pengiriman_dokuman' => \$record->id, 'tipe' => 'volume']) }}')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Hapus Laporan Volume">
HTML;
$content = str_replace($volumeSingleDeleteOld, $volumeSingleDeleteNew, $content);

// In ongkir table, it uses $cost->id
$ongkirSingleDeleteOld = <<<HTML
<button type="button" onclick="openDeleteModal('modalHapusLaporan', '{{ route('pengiriman-dokumen.destroy', \$cost->id) }}')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Hapus Laporan Keseluruhan">
HTML;
$ongkirSingleDeleteNew = <<<HTML
<button type="button" onclick="openDeleteModal('modalHapusLaporan', '{{ route('pengiriman-dokumen.destroy', ['pengiriman_dokuman' => \$cost->id, 'tipe' => 'ongkir']) }}')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Hapus Laporan Ongkir">
HTML;
$content = str_replace($ongkirSingleDeleteOld, $ongkirSingleDeleteNew, $content);

file_put_contents($filePath, $content);
echo "Blade template patched successfully.\n";
