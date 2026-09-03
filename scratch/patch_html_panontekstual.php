<?php

$file = 'resources/views/non-teknik-non-tekstual.blade.php';
$content = file_get_contents($file);

// Add Hapus Semua button
$target_btn = '<x-button variant="primary" onclick="openModalTambah()"';
$replacement_btn = <<<'EOF'
            <button type="button" id="btnModeBulkPa" onclick="toggleBulkModePa()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Semua
            </button>
            <x-button variant="primary" onclick="openModalTambah()"
EOF;

if (strpos($content, 'id="btnModeBulkPa"') === false) {
    $content = str_replace($target_btn, $replacement_btn, $content);
}

// Add Form
$target_form_place = '<div class="overflow-x-auto';
$replacement_form_place = <<<'EOF'
        <!-- BULK DELETE FORM -->
        <form id="bulkDeleteFormPa" action="{{ route('non-teknik-non-tekstual.destroyBulk') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="filter_tahun" value="{{ $filterTahun }}">
            <input type="hidden" name="filter_bulan" value="{{ $filterBulan }}">
            <input type="hidden" name="delete_all" id="deleteAllFlagPa" value="0">
            
            <div id="btnGroupBulkPa" class="hidden mb-3 bg-red-50 border border-red-200 p-3 rounded-xl flex items-center justify-between">
                <span class="text-xs font-semibold text-red-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Data terpilih akan dihapus permanen.
                </span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAllPa()" class="px-3 py-1.5 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="button" onclick="submitBulkDeletePa()" class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Terpilih
                    </button>
                </div>
            </div>
            
        <div class="overflow-x-auto
EOF;

if (strpos($content, 'id="bulkDeleteFormPa"') === false) {
    $content = str_replace($target_form_place, $replacement_form_place, $content);
}

// Add hide-bulk class
$content = str_replace('<div class="overflow-x-auto">', '<div class="overflow-x-auto hide-bulk" id="tableContainerBulkPa">', $content);
$content = str_replace('<div class="overflow-x-auto hide-bulk" id="tableContainerBulkPa">', '<div class="overflow-x-auto hide-bulk" id="tableContainerBulkPa">', $content); // to ensure no duplicates if already exists

// Fix the headers
$target_head = "                \$headers = ['Tahun', 'Bulan'];";
$replacement_head = "                \$headers = ['<input type=\"checkbox\" id=\"selectAllBulkPa\" class=\"bulk-cb-header hidden\" onclick=\"toggleSelectAllPa()\">', 'Tahun', 'Bulan'];";
if (strpos($content, 'id="selectAllBulkPa"') === false) {
    $content = str_replace($target_head, $replacement_head, $content);
}

// Fix rows
$target_row = <<<'EOF'
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
EOF;
$replacement_row = <<<'EOF'
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-center w-10">
                            <input type="checkbox" name="ids[]" value="{{ $row['tahun'] }}|{{ $row['bulan'] }}" class="cb-bulk hidden w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500" onclick="toggleCheckboxPa()">
                        </td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
EOF;
if (strpos($content, 'cb-bulk hidden') === false) {
    $content = str_replace($target_row, $replacement_row, $content);
}

// End form
if (strpos($content, '</form>', strpos($content, '</x-table>')) === false) {
    $content = str_replace('</x-table>', "</x-table>\n        </form>", $content);
}

// Fix Subtotal Colspan
if (strpos($content, '<td colspan="2" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>') !== false) {
    $content = str_replace('<td colspan="2" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>', '<td colspan="3" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>', $content);
}

// Fix CSS
$css = <<<CSS
<style>
    .hide-bulk .cb-bulk, .hide-bulk .bulk-cb-header { display: none !important; }
    .hide-bulk th:first-child, .hide-bulk td:first-child { width: 0; padding: 0; overflow: hidden; opacity: 0; }
</style>
CSS;
if (strpos($content, '.hide-bulk .cb-bulk') === false) {
    $content = str_replace("@section('content')", "@section('content')\n" . $css, $content);
}


file_put_contents($file, $content);
echo "HTML parts patched.\n";
