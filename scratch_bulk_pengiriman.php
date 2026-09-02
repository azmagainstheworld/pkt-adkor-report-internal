<?php
$content = file_get_contents('resources/views/pengiriman-dokumen.blade.php');

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

// 2. TABEL 1 (VOLUME)
$bulkBtn1 = <<<EOD
                <button type="button" id="btnModeBulkVolume" onclick="toggleBulkModeVolume()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>
EOD;
$content = str_replace('<button type="button" onclick="openModalTambahVolume()"', $bulkBtn1 . "\n                <button type=\"button\" onclick=\"openModalTambahVolume()\"", $content);

$actionBar1 = <<<EOD
        <form id="bulkDeleteFormVolume" action="{{ route('pengiriman-dokumen.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data volume dokumen terpilih?')">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulkVolume" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100 relative z-10">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAllVolume()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulkVolume" class="hide-bulk overflow-x-auto">
EOD;
$content = str_replace('<div class="overflow-x-auto">', $actionBar1, $content);

// In Table 1, replace headers
$content = str_replace("\$tableHeaders = ['Tahun', 'Bulan',", "\$tableHeaders = ['<input type=\"checkbox\" id=\"selectAllBulkVolume\" onclick=\"toggleSelectAllVolume()\">', 'Tahun', 'Bulan',", $content);

$content = preg_replace('/(<tr><td colspan=")\d+(" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data laporan volume\.)/', '$1{{ count($tableHeaders) }}$2', $content);

$content = preg_replace('/(<tr class="hover:bg-gray-50 transition-colors text-sm">)/s', 
    "$1\n                        <td class=\"px-6 py-4 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk-volume\" value=\"{{ \$record->id }}\" onclick=\"toggleCheckboxVolume()\"></td>", $content);

$content = preg_replace('/<\/x-table>\s*<\/div>\s*<!-- PAGINATION TABEL 1 -->/s', "</x-table>\n            </div>\n        </form>\n        <!-- PAGINATION TABEL 1 -->", $content, 1);


// 3. TABEL 2 (ONGKIR)
$bulkBtn2 = <<<EOD
                <button type="button" id="btnModeBulkOngkir" onclick="toggleBulkModeOngkir()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>
EOD;
$content = str_replace('<button type="button" onclick="openModalTambahOngkir()"', $bulkBtn2 . "\n                <button type=\"button\" onclick=\"openModalTambahOngkir()\"", $content);

$actionBar2 = <<<EOD
        <form id="bulkDeleteFormOngkir" action="{{ route('pengiriman-dokumen.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data biaya ongkir terpilih?')">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulkOngkir" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100 relative z-10">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAllOngkir()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulkOngkir" class="hide-bulk overflow-x-auto w-full">
                <x-table :headers="['<input type=\'checkbox\' id=\'selectAllBulkOngkir\' onclick=\'toggleSelectAllOngkir()\'>', 'Tahun', 'Bulan', 'Total Ongkir Dalam Negeri', 'Total Ongkir Luar Negeri', 'Aksi']">
EOD;
$content = str_replace('<x-table :headers="[\'Tahun\', \'Bulan\', \'Total Ongkir Dalam Negeri\', \'Total Ongkir Luar Negeri\', \'Aksi\']">', $actionBar2, $content);

$content = preg_replace('/(<tr class="hover:bg-gray-50 transition-colors text-sm">\s*<td class="px-6 py-4 text-gray-700 font-medium text-center align-middle">{{ \$cost->tahun }})/s', 
    "<tr class=\"hover:bg-gray-50 transition-colors text-sm\">\n                    <td class=\"px-6 py-4 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk-ongkir\" value=\"{{ \$cost->id }}\" onclick=\"toggleCheckboxOngkir()\"></td>\n                    <td class=\"px-6 py-4 text-gray-700 font-medium text-center align-middle\">{{ \$cost->tahun }}", $content);

$content = str_replace('colspan="5"', 'colspan="6"', $content);
$content = str_replace('<td colspan="2" class="px-6 py-4 text-right">Total', '<td colspan="3" class="px-6 py-4 text-right">Total', $content);

$content = preg_replace('/<\/x-table>\s*<!-- PAGINATION TABEL 2 -->/s', "</x-table>\n            </div>\n        </form>\n        <!-- PAGINATION TABEL 2 -->", $content, 1);

// 4. Add JS script
$js = <<<EOD
    <script>
        // TABEL 1 (VOLUME)
        function toggleBulkModeVolume() {
            let container = document.getElementById("tableContainerBulkVolume");
            let btn = document.getElementById("btnModeBulkVolume");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllVolume();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllVolume() {
            let selectAll = document.getElementById("selectAllBulkVolume");
            let checkboxes = document.querySelectorAll(".cb-bulk-volume");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnVolume();
        }
        function toggleCheckboxVolume() {
            let selectAll = document.getElementById("selectAllBulkVolume");
            let checkboxes = document.querySelectorAll(".cb-bulk-volume");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnVolume();
        }
        function toggleDeleteBtnVolume() {
            let group = document.getElementById("btnGroupBulkVolume");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-volume:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllVolume() {
            let selectAll = document.getElementById("selectAllBulkVolume");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-volume");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnVolume();
        }

        // TABEL 2 (ONGKIR)
        function toggleBulkModeOngkir() {
            let container = document.getElementById("tableContainerBulkOngkir");
            let btn = document.getElementById("btnModeBulkOngkir");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllOngkir();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllOngkir() {
            let selectAll = document.getElementById("selectAllBulkOngkir");
            let checkboxes = document.querySelectorAll(".cb-bulk-ongkir");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnOngkir();
        }
        function toggleCheckboxOngkir() {
            let selectAll = document.getElementById("selectAllBulkOngkir");
            let checkboxes = document.querySelectorAll(".cb-bulk-ongkir");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnOngkir();
        }
        function toggleDeleteBtnOngkir() {
            let group = document.getElementById("btnGroupBulkOngkir");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-ongkir:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllOngkir() {
            let selectAll = document.getElementById("selectAllBulkOngkir");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-ongkir");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnOngkir();
        }
EOD;

$content = str_replace('<script>', $js . "\n\n<script>", $content);

file_put_contents('resources/views/pengiriman-dokumen.blade.php', $content);
echo "Pengiriman Dokumen view updated for bulk delete.\n";
