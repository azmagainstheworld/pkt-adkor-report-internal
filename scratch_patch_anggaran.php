<?php
$content = file_get_contents('resources/views/anggaran/index.blade.php');

// 1. Add CSS
if (strpos($content, '.hide-bulk ') === false) {
    $css = <<<EOD
<style>
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>

<!-- Main Content -->
EOD;
    $content = str_replace('<!-- Main Content -->', $css, $content);
}

// 2. Add Button Mode Bulk near Tambah Data
if (strpos($content, 'toggleBulkMode()') === false) {
    // Search for "Tambah Data"
    $btn = <<<EOD
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex justify-center items-center gap-2 rounded-xl border border-red-200 text-red-600 bg-red-50 px-4 py-2 text-sm font-medium hover:bg-red-100 transition-colors shadow-sm">
                    Mode Hapus Massal
                </button>
                <button
EOD;
    $content = preg_replace('/<button[^>]+onclick="openModal\(\'modalTambahAnggaran\'[^>]+>/', $btn . "$0", $content);
}

// 3. Wrap Table in Form
if (strpos($content, 'id="bulkDeleteForm"') === false) {
    $formStart = <<<EOD
    <form id="bulkDeleteForm" action="{{ route('anggaran.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data anggaran terpilih?')">
        @csrf
        @method('DELETE')
        
        <div id="btnGroup" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 rounded-t-xl border-b border-red-100 mt-6">
            <span class="text-xs text-red-600 font-semibold flex items-center gap-2">
                <span id="selectedCount">0</span> data terpilih
            </span>
            <div class="flex gap-2">
                <button type="button" onclick="cancelAll()" class="px-3 py-1.5 text-xs bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-3 py-1.5 text-xs text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus Terpilih</button>
            </div>
        </div>

        <x-card class="!rounded-xl shadow-sm border border-gray-100 mb-8 hide-bulk" id="tableContainer">
EOD;
    // Anggaran uses "TABEL 1" or something. Let's find the card that contains the detail table.
    // It's usually `<x-card class="!rounded-xl shadow-sm border border-gray-100 mb-8">`
    $content = preg_replace('/<x-card class="!rounded-xl shadow-sm border border-gray-100 mb-8">/', $formStart, $content, 1);
    
    // Add </form> at the end of THAT card
    // Since it's hard to find the matching </x-card>, I'll just find the first one after tableContainer.
    // Wait, better to replace `<div class="p-4 border-t border-gray-100 bg-white">` area and add </form> after `</x-card>`
    $content = preg_replace('/(<\/x-card>)/', "$1\n    </form>", $content, 1);
}

// 4. Update Header Array for $headersDetail
if (strpos($content, "'<input type=\"checkbox\" id=\"selectAll\"") === false) {
    $headerPatch = <<<EOD
        @php
            \$headersDetail = ['<input type="checkbox" id="selectAll" onclick="toggleSelectAll()" class="rounded border-gray-300 text-red-600 focus:ring-red-500">', 'No', 'Kategori', 'Tahun', 'Bulan', 'Rincian', 'RKAP', 'Komitmen', 'Realisasi', 'Sisa Anggaran', 'Aksi'];
        @endphp
EOD;
    $content = preg_replace('/@php\s*\$headersDetail = \[\'No\', [^\]]+\];\s*@endphp/', $headerPatch, $content);
}

// 5. Update TD for the detail table
if (strpos($content, '<input type="checkbox" name="ids[]"') === false) {
    $tdPatch = <<<EOD
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="text-center">
                        <input type="checkbox" name="ids[]" class="cb-item rounded border-gray-300 text-red-600 focus:ring-red-500" value="{{ \$item->id }}" onclick="toggleCheckbox()">
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500">
EOD;
    $content = preg_replace('/<tr class="hover:bg-gray-50 transition-colors">\s*<td class="px-4 py-3 text-center text-gray-500">/', $tdPatch, $content);
}

// 6. Add JS Script at the end of file
if (strpos($content, 'function toggleBulkMode') === false) {
    $js = <<<EOD
<script>
function toggleBulkMode() {
    let container = document.getElementById("tableContainer");
    if (container.classList.contains("hide-bulk")) {
        container.classList.remove("hide-bulk");
    } else {
        container.classList.add("hide-bulk");
        cancelAll();
    }
}
function toggleSelectAll() {
    let selectAll = document.getElementById("selectAll");
    let checkboxes = document.querySelectorAll(".cb-item");
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    toggleDeleteButton();
}
function toggleCheckbox() {
    let selectAll = document.getElementById("selectAll");
    let checkboxes = document.querySelectorAll(".cb-item");
    let allChecked = Array.from(checkboxes).every(cb => cb.checked);
    selectAll.checked = allChecked;
    toggleDeleteButton();
}
function toggleDeleteButton() {
    let checkboxes = document.querySelectorAll(".cb-item");
    let checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
    let btnGroup = document.getElementById("btnGroup");
    document.getElementById("selectedCount").innerText = checkedCount;
    if (checkedCount > 0) {
        btnGroup.classList.remove("hidden");
    } else {
        btnGroup.classList.add("hidden");
    }
}
function cancelAll() {
    let selectAll = document.getElementById("selectAll");
    let checkboxes = document.querySelectorAll(".cb-item");
    if(selectAll) selectAll.checked = false;
    checkboxes.forEach(cb => cb.checked = false);
    toggleDeleteButton();
}
</script>
EOD;
    $content = preg_replace('/@endsection\s*$/s', "\n" . $js . "\n@endsection", $content);
}

file_put_contents('resources/views/anggaran/index.blade.php', $content);
echo "anggaran/index.blade.php updated.\n";
