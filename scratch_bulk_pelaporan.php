<?php
$content = file_get_contents('resources/views/pelaporan.blade.php');

// 1. Add CSS
if (strpos($content, '.hide-bulk') === false) {
    $css = <<<EOD
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>
EOD;
    $content = preg_replace('/(<x-app-layout[^>]*>)/', "$1\n" . $css, $content);
}

// 2. Add "Mode Hapus Massal" button
$bulkBtn = <<<EOD
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>
EOD;
$content = str_replace('<x-button variant="primary" onclick="openModalTambah()"', $bulkBtn . "\n                <x-button variant=\"primary\" onclick=\"openModalTambah()\"", $content);

// 3. Form and Action Bar
$actionBar = <<<EOD
        <form id="bulkDeleteForm" action="{{ route('pelaporan.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulk" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulk" class="hide-bulk overflow-x-auto">
EOD;

$content = str_replace('<div class="overflow-x-auto">', $actionBar, $content);
$content = preg_replace('/<\/x-table>\s*<\/div>\s*<div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">/s', "</x-table>\n            </div>\n        </form>\n        <div class=\"p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white\">", $content, 1);

// 4. Update Headers
$content = str_replace("\$tableHeaders = ['Tujuan Laporan'", "\$tableHeaders = ['<input type=\"checkbox\" id=\"selectAllBulk\" onclick=\"toggleSelectAll()\">', 'Tujuan Laporan'", $content);

// 5. Update row
$content = preg_replace('/(<tr class="pelaporan-row hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ \$index % 2 == 1 \? \'bg-gray-50\/60\' : \'\' }}">)/s', 
    "$1\n                        <td class=\"px-3 py-2 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk\" value=\"{{ \$item->id }}\" onclick=\"toggleCheckbox()\"></td>", $content);

// Update colspan empty state
$content = preg_replace('/(<tr>\s*<td colspan=")10(" class="px-6 py-10 text-center text-gray-500 text-sm">Tidak ada rincian pelaporan\.)/', '${1}11${2}', $content);

// 6. Add JS script
$js = <<<EOD
    <script>
        function toggleBulkMode() {
            let container = document.getElementById("tableContainerBulk");
            let btn = document.getElementById("btnModeBulk");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtn();
        }
        function toggleCheckbox() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn();
        }
        function toggleDeleteBtn() {
            let group = document.getElementById("btnGroupBulk");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll() {
            let selectAll = document.getElementById("selectAllBulk");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn();
        }
EOD;

$content = str_replace('<script>', $js . "\n\n<script>", $content);

file_put_contents('resources/views/pelaporan.blade.php', $content);
echo "Pelaporan view updated for bulk delete.\n";
