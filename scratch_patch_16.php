<?php

$path = 'resources/views/anggaran/index.blade.php';
$content = file_get_contents($path);

// 1. Add CSS
if (strpos($content, '.hide-bulk th:first-child') === false) {
    $content = str_replace('<main class="', "<style>\n.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }\n</style>\n<main class=\"", $content);
}

// 2. Add Hapus Semua button in Action Bar
$btnTambahStr = '<button type="button" onclick="openModalTambah()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga hover:bg-orange-600 rounded-xl shadow-sm transition-colors border-none outline-none focus:ring-2 focus:ring-orange-300">';
$btnHapusSemua = <<<HTML
            <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex justify-center items-center gap-2 rounded-xl border border-red-200 text-red-600 bg-red-50 px-4 py-2.5 text-sm font-medium hover:bg-red-100 transition-colors shadow-sm mr-3">
                Hapus Semua
            </button>
HTML;
if (strpos($content, 'id="btnModeBulk"') === false) {
    $content = str_replace($btnTambahStr, $btnHapusSemua . "\n            " . $btnTambahStr, $content);
}

// 3. Wrap Table in Form & add btnGroupBulk
$tableCardOld = '<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 mb-8 bg-white">';
$tableFormStart = <<<HTML
    <form id="bulkDeleteForm" action="{{ route('anggaran.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
        @csrf
        @method('DELETE')
        
        <div id="btnGroupBulk" class="hidden justify-between items-center px-4 py-2 bg-red-50 rounded-t-xl border-b border-red-100">
            <span class="text-xs text-red-600 font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span id="selectedCount">0</span> data terpilih untuk dihapus
            </span>
            <div class="flex gap-2">
                <button type="button" onclick="cancelAll()" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm flex items-center gap-1.5">
                    Hapus Terpilih
                </button>
            </div>
        </div>

    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 mb-8 bg-white hide-bulk" id="tableContainerBulk">
HTML;
if (strpos($content, 'id="bulkDeleteForm"') === false) {
    // Only replace the first occurrence (TABEL 1)
    $pos = strpos($content, $tableCardOld);
    if ($pos !== false) {
        $content = substr_replace($content, $tableFormStart, $pos, strlen($tableCardOld));
    }
    
    // Close the form after the card ends
    // The card ends right before <!-- ================= TABEL 2
    $table2Marker = '<!-- ================= TABEL 2';
    $content = str_replace($table2Marker, "</form>\n\n    " . $table2Marker, $content);
}

// 4. Update Headers
$headerOld = "\$tableHeadersRincian = ['Tahun', 'Bulan', 'Detail', 'Anggaran RKAP', 'Komitmen', 'Realisasi', 'Realisasi + Komitmen', '% Realisasi+Komitmen', 'Sisa Anggaran', '% Sisa Anggaran'];";
$headerNew = "\$tableHeadersRincian = ['<input type=\"checkbox\" id=\"selectAllBulk\" onclick=\"toggleSelectAll()\">', 'Tahun', 'Bulan', 'Detail', 'Anggaran RKAP', 'Komitmen', 'Realisasi', 'Realisasi + Komitmen', '% Realisasi+Komitmen', 'Sisa Anggaran', '% Sisa Anggaran'];";
$content = str_replace($headerOld, $headerNew, $content);

// 5. Update Rows and Colspans
$trOld = '<tr class="hover:bg-gray-100 transition-colors text-sm">';
$trNew = '<tr class="hover:bg-gray-100 transition-colors text-sm">' . "\n" . '                                <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk" value="{{ $anggaran->id }}" onclick="toggleCheckbox()"></td>';
if (strpos($content, 'class="cb-bulk"') === false) {
    // Only apply to the first table's TRs, but since other tables don't have this exact structure, it's fine. Wait, other tables use the same TR class.
    // Let's be safer and replace based on the $anggaran object usage.
    $trContextOld = '<tr class="hover:bg-gray-100 transition-colors text-sm">
                                <td class="px-6 py-3 text-gray-700 text-center align-middle whitespace-nowrap">{{ $entry[\'tahun\'] }}</td>';
    $trContextNew = '<tr class="hover:bg-gray-100 transition-colors text-sm">
                                <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk" value="{{ $anggaran->id }}" onclick="toggleCheckbox()"></td>
                                <td class="px-6 py-3 text-gray-700 text-center align-middle whitespace-nowrap">{{ $entry[\'tahun\'] }}</td>';
    $content = str_replace($trContextOld, $trContextNew, $content);
    
    // Colspans
    $colspanCatOld = 'colspan="{{ 9 + (isset($kolomDinamis) ? count($kolomDinamis) : 0) }}"';
    $colspanCatNew = 'colspan="{{ 10 + (isset($kolomDinamis) ? count($kolomDinamis) : 0) }}"';
    $content = str_replace($colspanCatOld, $colspanCatNew, $content);

    $colspanSubOld = '<td class="px-6 py-4 font-extrabold text-gray-900 text-right align-middle" colspan="3">SUBTOTAL';
    $colspanSubNew = '<td class="px-6 py-4 font-extrabold text-gray-900 text-right align-middle" colspan="4">SUBTOTAL';
    $content = str_replace($colspanSubOld, $colspanSubNew, $content);
    
    $emptyRowOld = '<tr><td colspan="15" class="px-6 py-10';
    $emptyRowNew = '<tr><td colspan="16" class="px-6 py-10';
    $content = str_replace($emptyRowOld, $emptyRowNew, $content);
}

// 6. Clean up old JS and insert new Bulk Delete JS
// Remove existing function toggleBulkMode if any
$content = preg_replace('/function toggleBulkMode\(\) \{[\s\S]*?function cancelAll\(\) \{[\s\S]*?\}\s*\}/', '', $content);
$content = preg_replace('/function toggleBulkMode\(\) \{[\s\S]*?cancelAll\(\);[\s\S]*?\}/', '', $content);

$jsBulkNew = <<<JS
<script>
let isBulkMode = false;

function toggleBulkMode() {
    isBulkMode = !isBulkMode;
    const container = document.getElementById('tableContainerBulk');
    const btnGroup = document.getElementById('btnGroupBulk');
    if (container) {
        if (isBulkMode) {
            container.classList.remove('hide-bulk');
            if(btnGroup) {
                btnGroup.classList.remove('hidden');
                btnGroup.classList.add('flex');
            }
        } else {
            container.classList.add('hide-bulk');
            if(btnGroup) {
                btnGroup.classList.add('hidden');
                btnGroup.classList.remove('flex');
            }
            cancelAllBtnOnly();
        }
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAllBulk');
    const checkboxes = document.querySelectorAll('.cb-bulk');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function toggleCheckbox() {
    const selectAll = document.getElementById('selectAllBulk');
    const checkboxes = document.querySelectorAll('.cb-bulk');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    if(selectAll) selectAll.checked = allChecked;
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.cb-bulk:checked').length;
    const countEl = document.getElementById('selectedCount');
    if (countEl) countEl.textContent = count;
}

function cancelAllBtnOnly() {
    const selectAll = document.getElementById('selectAllBulk');
    if (selectAll) selectAll.checked = false;
    const checkboxes = document.querySelectorAll('.cb-bulk');
    checkboxes.forEach(cb => cb.checked = false);
    updateSelectedCount();
}

function cancelAll() {
    cancelAllBtnOnly();
    isBulkMode = false;
    const container = document.getElementById('tableContainerBulk');
    if (container) container.classList.add('hide-bulk');
    const btnGroup = document.getElementById('btnGroupBulk');
    if (btnGroup) {
        btnGroup.classList.add('hidden');
        btnGroup.classList.remove('flex');
    }
}
</script>
JS;

if (strpos($content, 'function toggleBulkMode()') === false) {
    $content = str_replace('@endsection', $jsBulkNew . "\n@endsection", $content);
}

// 7. Ensure Atur Kolom is wrapped in RBAC
$aturKolomRegex = '/(<!-- Kategori 2: Atur Kolom -->.*?Atur Kolom Tabel\s*<\/button>\s*<\/div>)/s';
$aturKolomReplacement = "@if(auth()->check() && auth()->user()->isAdmin())\n$1\n@endif";
if (strpos($content, '@if(auth()->check() && auth()->user()->isAdmin())') === false || !preg_match('/@if\(auth\(\)->check\(\) && auth\(\)->user\(\)->isAdmin\(\)\)\s*<!-- Kategori 2: Atur Kolom -->/s', $content)) {
    $content = preg_replace($aturKolomRegex, $aturKolomReplacement, $content);
}

file_put_contents($path, $content);
echo "Anggaran index view patched successfully!\n";
?>
