<?php
$content = file_get_contents('resources/views/undangan.blade.php');

// 1. Add CSS
if (strpos($content, '.hide-bulk-detail') === false) {
    $css = <<<EOD
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk-detail th:first-child, .hide-bulk-detail td:first-child { display: none !important; }
</style>
EOD;
    $content = preg_replace('/(<x-app-layout[^>]*>)/', "$1\n" . $css, $content);
}

// 2. Add "Mode Hapus Massal" button
$bulkBtn = <<<EOD
                <button type="button" id="btnModeBulkDetail" onclick="toggleBulkMode('detail')" class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors text-xs font-semibold flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>
EOD;
$content = str_replace('<x-button variant="primary" onclick="openModal(\'modalTambahDetail\')"', $bulkBtn . "\n                <x-button variant=\"primary\" onclick=\"openModal('modalTambahDetail')\"", $content);

// 3. Wrap table in form and add Action Bar
$actionBar = <<<EOD
        <form id="bulkDeleteDetailForm" action="{{ route('undangan.detail.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupDetail" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll('detail')" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerDetail" class="hide-bulk-detail overflow-x-auto">
EOD;

$content = str_replace('<div class="overflow-x-auto">
            <x-table :headers="[\'No.\', \'Tahun\', \'Bulan\', \'Jenis Undangan\', \'Agenda\', \'Aksi\']">', 
$actionBar . '
            <x-table :headers="[\'<input type=\\\'checkbox\\\' id=\\\'selectAllDetail\\\' onclick=\\\'toggleSelectAll(\"detail\")\\\'>\', \'No.\', \'Tahun\', \'Bulan\', \'Jenis Undangan\', \'Agenda\', \'Aksi\']">', $content);

$content = str_replace('</x-table>
        </div>', '</x-table>
            </div>
        </form>', $content);

// 4. Add Checkbox to rows
$content = preg_replace('/(<tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-100 last:border-0">\s*<td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">)/s', 
    "<tr class=\"hover:bg-gray-50 transition-colors text-sm border-b border-gray-100 last:border-0\">\n                        <td class=\"px-6 py-4 text-center\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-detail\" value=\"{{ \$detail->id }}\" onclick=\"toggleCheckbox('detail')\"></td>\n                        <td class=\"px-6 py-4 text-gray-700 font-medium whitespace-nowrap\">", $content);

$content = preg_replace('/(<tr><td colspan=")6(")/', '${1}7${2}', $content); // colspan=6 to 7 for empty row

// 5. Add JS script
$js = <<<EOD
    <script>
        function toggleBulkMode(tipe) {
            let container = document.getElementById("tableContainer" + (tipe === "detail" ? "Detail" : ""));
            let btn = document.getElementById("btnModeBulk" + (tipe === "detail" ? "Detail" : ""));
            if (container.classList.contains("hide-bulk-" + tipe)) {
                container.classList.remove("hide-bulk-" + tipe);
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk-" + tipe);
                cancelAll(tipe);
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll(tipe) {
            let selectAll = document.getElementById("selectAll" + (tipe === "detail" ? "Detail" : ""));
            let checkboxes = document.querySelectorAll(".cb-" + tipe);
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtn(tipe);
        }
        function toggleCheckbox(tipe) {
            let selectAll = document.getElementById("selectAll" + (tipe === "detail" ? "Detail" : ""));
            let checkboxes = document.querySelectorAll(".cb-" + tipe);
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn(tipe);
        }
        function toggleDeleteBtn(tipe) {
            let group = document.getElementById("btnGroup" + (tipe === "detail" ? "Detail" : ""));
            if (group) {
                let checked = document.querySelectorAll(".cb-" + tipe + ":checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll(tipe) {
            let selectAll = document.getElementById("selectAll" + (tipe === "detail" ? "Detail" : ""));
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-" + tipe);
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn(tipe);
        }
EOD;

$content = str_replace('<script>', $js, $content);

file_put_contents('resources/views/undangan.blade.php', $content);
echo "Undangan view updated for bulk delete.\n";
