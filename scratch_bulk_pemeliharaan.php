<?php
$content = file_get_contents('resources/views/pemeliharaan.blade.php');

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

// 2. TABEL 1 (RUTIN)
$bulkBtnRutin = <<<EOD
                <button type="button" id="btnModeBulkRutin" onclick="toggleBulkModeRutin()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Mode Hapus Massal
                </button>
EOD;
$content = str_replace('<x-button variant="primary" onclick="openModalTambahRutin()"', $bulkBtnRutin . "\n                <x-button variant=\"primary\" onclick=\"openModalTambahRutin()\"", $content);

$actionBarRutin = <<<EOD
        <form id="bulkDeleteFormRutin" action="{{ route('pemeliharaan-rutin.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data pemeliharaan rutin terpilih?')">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulkRutin" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAllRutin()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulkRutin" class="hide-bulk overflow-x-auto">
EOD;

$content = preg_replace('/(<div class="overflow-x-auto">\s*@php\s*\$headersRutin = \[\'Tahun\', \'Bulan\'\];)/s', $actionBarRutin . "\n            @php\n                \$headersRutin = ['<input type=\"checkbox\" id=\"selectAllBulkRutin\" onclick=\"toggleSelectAllRutin()\">', 'Tahun', 'Bulan'];", $content);

$content = preg_replace('/(<tr><td colspan=")\{\{ count\(\$headersRutin\) \}\}(" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data pemeliharaan rutin\.)/', '${1}{{ count($headersRutin) }}$2', $content);

$content = preg_replace('/(<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">)\s*(<td class="px-4 py-3 text-gray-700 font-medium text-center">\{\{ \$row\[\'tahun\'\] \}\}<\/td>)/s', 
    "$1\n                        <td class=\"px-3 py-2 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk-rutin\" value=\"{{ \$row['tahun'] }}|{{ \$row['bulan'] }}\" onclick=\"toggleCheckboxRutin()\"></td>\n                        $2", $content, 1); // Limit 1 just in case, but actually there's only one loop. Let's not use limit if it's safe. It's safe.

$content = preg_replace('/<\/x-table>\s*<\/div>\s*<\/x-card>/s', "</x-table>\n            </div>\n        </form>\n    </x-card>", $content, 1);


// 3. TABEL 2 (PERALATAN)
$bulkBtnPeralatan = <<<EOD
                <button type="button" id="btnModeBulkPeralatan" onclick="toggleBulkModePeralatan()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Mode Hapus Massal
                </button>
EOD;
$content = str_replace('<x-button variant="primary" onclick="openModalTambahPeralatan()"', $bulkBtnPeralatan . "\n                <x-button variant=\"primary\" onclick=\"openModalTambahPeralatan()\"", $content);

$actionBarPeralatan = <<<EOD
        <form id="bulkDeleteFormPeralatan" action="{{ route('pemeliharaan-peralatan.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data perbaikan peralatan terpilih?')">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulkPeralatan" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAllPeralatan()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulkPeralatan" class="hide-bulk overflow-x-auto">
EOD;

// The second table also starts with `<div class="overflow-x-auto">` followed by `@php $headersAlat = ['Tahun', 'Bulan'];`
$content = preg_replace('/(<div class="overflow-x-auto">\s*@php\s*\$headersAlat = \[\'Tahun\', \'Bulan\'\];)/s', $actionBarPeralatan . "\n            @php\n                \$headersAlat = ['<input type=\"checkbox\" id=\"selectAllBulkPeralatan\" onclick=\"toggleSelectAllPeralatan()\">', 'Tahun', 'Bulan'];", $content);

$content = preg_replace('/(<tr><td colspan=")\{\{ count\(\$headersAlat\) \}\}(" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data rincian perbaikan\.)/', '${1}{{ count($headersAlat) }}$2', $content);

// For Peralatan row
$content = preg_replace('/(<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">)\s*(<td class="px-4 py-3 text-gray-700 font-medium text-center">\{\{ \$row\[\'tahun\'\] \}\}<\/td>)/s', 
    "$1\n                        <td class=\"px-3 py-2 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk-peralatan\" value=\"{{ \$row['tahun'] }}|{{ \$row['bulan'] }}\" onclick=\"toggleCheckboxPeralatan()\"></td>\n                        $2", $content);

$content = preg_replace('/<\/x-table>\s*<\/div>\s*<\/x-card>\s*<!-- MODAL HAPUS KONFIRMASI -->/s', "</x-table>\n            </div>\n        </form>\n    </x-card>\n\n    <!-- MODAL HAPUS KONFIRMASI -->", $content, 1);


// 4. Add JS script
$js = <<<EOD
    <script>
        // TABEL 1 (RUTIN)
        function toggleBulkModeRutin() {
            let container = document.getElementById("tableContainerBulkRutin");
            let btn = document.getElementById("btnModeBulkRutin");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllRutin();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnRutin();
        }
        function toggleCheckboxRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnRutin();
        }
        function toggleDeleteBtnRutin() {
            let group = document.getElementById("btnGroupBulkRutin");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-rutin:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnRutin();
        }

        // TABEL 2 (PERALATAN)
        function toggleBulkModePeralatan() {
            let container = document.getElementById("tableContainerBulkPeralatan");
            let btn = document.getElementById("btnModeBulkPeralatan");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllPeralatan();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllPeralatan() {
            let selectAll = document.getElementById("selectAllBulkPeralatan");
            let checkboxes = document.querySelectorAll(".cb-bulk-peralatan");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnPeralatan();
        }
        function toggleCheckboxPeralatan() {
            let selectAll = document.getElementById("selectAllBulkPeralatan");
            let checkboxes = document.querySelectorAll(".cb-bulk-peralatan");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnPeralatan();
        }
        function toggleDeleteBtnPeralatan() {
            let group = document.getElementById("btnGroupBulkPeralatan");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-peralatan:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllPeralatan() {
            let selectAll = document.getElementById("selectAllBulkPeralatan");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-peralatan");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnPeralatan();
        }
EOD;

$content = str_replace('<script>', $js . "\n\n<script>", $content);

file_put_contents('resources/views/pemeliharaan.blade.php', $content);
echo "Pemeliharaan view updated for bulk delete.\n";
