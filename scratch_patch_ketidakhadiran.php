<?php
$files = [
    'resources/views/ketidakhadiran/index.blade.php' => 'ketidakhadiran.destroyBulananBulk',
    'resources/views/ketidakhadiran/harian.blade.php' => 'ketidakhadiran.destroyHarianBulk',
];

foreach ($files as $filePath => $routeName) {
    if (!file_exists($filePath)) continue;
    
    $content = file_get_contents($filePath);

    // 1. Add CSS
    if (strpos($content, '.hide-bulk ') === false) {
        $css = <<<EOD
<style>
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>

<!-- Main Scrollable Content -->
EOD;
        $content = str_replace('<!-- Main Scrollable Content -->', $css, $content);
    }

    // 2. Add Button Mode Bulk near Tambah Data
    if (strpos($content, 'toggleBulkMode()') === false) {
        // Search for "Tambah Data" or similar
        $btn = <<<EOD
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex justify-center items-center gap-2 rounded-xl border border-red-200 text-red-600 bg-red-50 px-4 py-2.5 text-sm font-medium hover:bg-red-100 transition-colors shadow-sm">
                    Mode Hapus Massal
                </button>
EOD;
        // In harian.blade.php, the button might be `onclick="bukaModalTambahHarian()"`
        // In index.blade.php, it might be `onclick="bukaModalTambahBulanan()"` or `onclick="bukaModalTambah()"`
        if (strpos($content, 'onclick="bukaModalTambahBulanan()"') !== false) {
            $content = str_replace('onclick="bukaModalTambahBulanan()"', 'onclick="bukaModalTambahBulanan()"' . ">\n" . '                    <svg', $content); // small fix
            $content = preg_replace('/(<button[^>]+onclick="bukaModalTambahBulanan\(\)"[^>]*>)/', $btn . "\n                $1", $content);
        } elseif (strpos($content, 'onclick="bukaModalTambahHarian()"') !== false) {
            $content = preg_replace('/(<button[^>]+onclick="bukaModalTambahHarian\(\)"[^>]*>)/', $btn . "\n                $1", $content);
        } else {
            // generic fallback
            $content = preg_replace('/(<button[^>]+onclick="bukaModalTambah\(\)"[^>]*>)/', $btn . "\n                $1", $content);
        }
    }

    // 3. Wrap Table in Form
    if (strpos($content, 'id="bulkDeleteForm"') === false) {
        $formStart = <<<EOD
    <!-- DATA TABLE SECTION -->
    <form id="bulkDeleteForm" action="{{ route('$routeName') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
        @csrf
        @method('DELETE')
        
        <div id="btnGroup" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 rounded-t-xl border-b border-red-100">
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

        <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 hide-bulk" id="tableContainer">
EOD;
        $content = preg_replace('/<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100">/', $formStart, $content, 1);
        $content = preg_replace('/(<\/x-card>)/', "$1\n    </form>", $content, 1);
    }

    // 4. Update Header Array
    if (strpos($content, "'<input type=\"checkbox\" id=\"selectAll\"") === false) {
        $content = preg_replace('/(@php\s*\$headers = \[\'No\', [^\]]+\];\s*)/', "@php\n            \$headers = ['<input type=\"checkbox\" id=\"selectAll\" onclick=\"toggleSelectAll()\" class=\"rounded border-gray-300 text-red-600 focus:ring-red-500\">', " . substr("$1", strpos("$1", "'No'")), $content);
    }

    // 5. Update TD
    if (strpos($content, '<input type="checkbox" name="ids[]"') === false) {
        $tdPatch = <<<EOD
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="text-center">
                        <input type="checkbox" name="ids[]" class="cb-item rounded border-gray-300 text-red-600 focus:ring-red-500" value="{{ \$item->id }}" onclick="toggleCheckbox()">
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-500">
EOD;
        $content = preg_replace('/<tr class="hover:bg-gray-50 transition-colors">\s*<td class="px-6 py-4 text-center text-sm font-medium text-gray-500">/', $tdPatch, $content);
        
        // Sometimes it's slightly different
        $tdPatch2 = <<<EOD
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="text-center">
                        <input type="checkbox" name="ids[]" class="cb-item rounded border-gray-300 text-red-600 focus:ring-red-500" value="{{ \$item->id }}" onclick="toggleCheckbox()">
                    </td>
                    <td class="px-6 py-4 text-center font-medium text-gray-500">
EOD;
        $content = preg_replace('/<tr class="hover:bg-gray-50 transition-colors">\s*<td class="px-6 py-4 text-center font-medium text-gray-500">/', $tdPatch2, $content);
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

    file_put_contents($filePath, $content);
    echo "$filePath updated.\n";
}
