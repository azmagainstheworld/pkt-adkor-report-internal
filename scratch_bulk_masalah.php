<?php
$content = file_get_contents('resources/views/masalah-kendala.blade.php');

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
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors text-xs font-semibold flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>
EOD;
$content = str_replace('<x-button variant="primary" onclick="openModalTambah()"', $bulkBtn . "\n                <x-button variant=\"primary\" onclick=\"openModalTambah()\"", $content);

// 3. Wrap table in form and add Action Bar
$actionBar = <<<EOD
        <form id="bulkDeleteForm" action="{{ route('masalah-kendala.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
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
$content = str_replace('</x-table>
        </div>', '</x-table>
            </div>
        </form>', $content);

// 4. Modify Headers to include checkbox
$content = preg_replace('/(\$tableHeaders = \[)\'No\',/', '$1\'<input type=\"checkbox\" id=\"selectAllBulk\" onclick=\"toggleSelectAll()\">\', \'No\',', $content);

// 5. Add Checkbox to rows
$content = preg_replace('/(<tr class="hover:bg-gray-50 transition-colors text-xs">\s*<td class="px-4 py-3 text-gray-700 font-medium text-center align-top">{{ \$index \+ 1 }})/s', 
    "<tr class=\"hover:bg-gray-50 transition-colors text-xs\">\n                        <td class=\"px-4 py-3 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk\" value=\"{{ \$row->id }}\" onclick=\"toggleCheckbox()\"></td>\n                        <td class=\"px-4 py-3 text-gray-700 font-medium text-center align-top\">{{ \$index + 1 }}", $content);

$content = preg_replace('/(<tr><td colspan="{{ count\(\$tableHeaders\) )("\s*class="px-4 py-8 text-center text-gray-500">)/', '${1}${2}', $content); // colspan is dynamic here so it's fine.

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

file_put_contents('resources/views/masalah-kendala.blade.php', $content);
echo "Masalah Kendala view updated for bulk delete.\n";
