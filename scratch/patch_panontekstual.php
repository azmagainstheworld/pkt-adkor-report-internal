<?php

$file = 'resources/views/non-teknik-non-tekstual.blade.php';
$content = file_get_contents($file);

// 1. Remove the syntax error
$content = str_replace("<script>\n    function openModal(id)", "    function openModal(id)", $content);

// 2. Add Hapus Semua button between Opsi Lanjutan and Tambah Data
$target_btn = <<<'EOF'
            <x-button variant="primary" onclick="openModalTambah()" class="!py-1.5 !px-3 text-xs bg-orange-600 hover:bg-orange-700 border-none">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Data
            </x-button>
        </div>
EOF;
$replacement_btn = <<<'EOF'
            <button type="button" id="btnModeBulkPa" onclick="toggleBulkModePa()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Semua
            </button>
            <x-button variant="primary" onclick="openModalTambah()" class="!py-1.5 !px-3 text-xs bg-orange-600 hover:bg-orange-700 border-none">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Data
            </x-button>
        </div>
        
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
EOF;
$content = str_replace($target_btn, $replacement_btn, $content);

// 3. Add tableContainerBulk class to the container and fix headers to include Checkbox
$target_container = '<div class="overflow-x-auto">';
$replacement_container = '<div class="overflow-x-auto hide-bulk" id="tableContainerBulkPa">';
$content = str_replace($target_container, $replacement_container, $content);

$target_headers = <<<'EOF'
            @php
                $headers = ['Tahun', 'Bulan'];
EOF;
$replacement_headers = <<<'EOF'
            @php
                $headers = ['<input type="checkbox" id="selectAllBulkPa" class="bulk-cb-header hidden" onclick="toggleSelectAllPa()">', 'Tahun', 'Bulan'];
EOF;
$content = str_replace($target_headers, $replacement_headers, $content);

// 4. Add Checkbox cell to table rows
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
$content = str_replace($target_row, $replacement_row, $content);

// 5. Close the form after </x-table>
$target_end_form = '</x-table>';
$replacement_end_form = "</x-table>\n        </form>";
$content = str_replace($target_end_form, $replacement_end_form, $content);

// 6. Fix Grand Total colspan
$target_total = '<td colspan="2" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>';
$replacement_total = '<td colspan="3" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>';
$content = str_replace($target_total, $replacement_total, $content);

// 7. Add CSS for hide-bulk
$css = <<<CSS
<style>
    .hide-bulk .cb-bulk, .hide-bulk .bulk-cb-header { display: none !important; }
    .hide-bulk th:first-child, .hide-bulk td:first-child { width: 0; padding: 0; overflow: hidden; opacity: 0; }
</style>
CSS;
$content = str_replace('@section(\'content\')', "@section('content')\n" . $css, $content);

// 8. Replace old buggy bulk JS with new one
$target_js_old_start = '        function toggleBulkMode1() {';
$target_js_old_end = '        function cancelAll2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn2();
        }';

$old_js_pattern = '/' . preg_quote($target_js_old_start, '/') . '.*?' . preg_quote($target_js_old_end, '/') . '/s';

$new_js = <<<JS
        function toggleBulkModePa() {
            let container = document.getElementById("tableContainerBulkPa");
            let btn = document.getElementById("btnModeBulkPa");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllPa();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllPa() {
            let selectAll = document.getElementById("selectAllBulkPa");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnPa();
        }
        function toggleCheckboxPa() {
            let selectAll = document.getElementById("selectAllBulkPa");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnPa();
        }
        function toggleDeleteBtnPa() {
            let group = document.getElementById("btnGroupBulkPa");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllPa() {
            let selectAll = document.getElementById("selectAllBulkPa");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnPa();
        }
        function submitBulkDeletePa() {
            let selectAll = document.getElementById("selectAllBulkPa");
            if (selectAll && selectAll.checked) {
                document.getElementById('deleteAllFlagPa').value = "1";
            } else {
                document.getElementById('deleteAllFlagPa').value = "0";
            }
            document.getElementById('bulkDeleteFormPa').submit();
        }
JS;

$content = preg_replace($old_js_pattern, $new_js, $content);

file_put_contents($file, $content);
echo "View patched for PA Non Tekstual.\n";
