<?php
$content = file_get_contents('resources/views/perizinan-perkantoran.blade.php');

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

// 2. TABEL 2 (TERBIT)
// Add "Hapus semua" button before openModalTambah()
$bulkBtn2 = <<<EOD
                <button type="button" id="btnModeBulkTerbit" onclick="toggleBulkModeTerbit()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>
EOD;
$content = str_replace('<x-button variant="primary" onclick="openModalTambah()"', $bulkBtn2 . "\n                <x-button variant=\"primary\" onclick=\"openModalTambah()\"", $content);

// Form and Action Bar
$actionBar2 = <<<EOD
        <form id="bulkDeleteFormTerbit" action="{{ route('perizinan-perkantoran.destroyBulk') }}" method="POST" >
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulkTerbit" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100 relative z-10">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAllTerbit()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulkTerbit" class="hide-bulk overflow-x-auto w-full">
                <x-table :headers="['<input type=\'checkbox\' id=\'selectAllBulkTerbit\' onclick=\'toggleSelectAllTerbit()\'>', 'NO', 'Tahun', 'Perizinan Terbit', 'Nomor', 'Terbit', 'Berakhir', 'Instansi Penerbit', 'Bulan', 'Kegiatan', 'Aksi']">
EOD;
$content = str_replace('<x-table :headers="[\'NO\', \'Tahun\', \'Perizinan Terbit\', \'Nomor\', \'Terbit\', \'Berakhir\', \'Instansi Penerbit\', \'Bulan\', \'Kegiatan\', \'Aksi\']">', $actionBar2, $content);
$content = preg_replace('/<\/x-table>\s*<div class="p-4 border-t border-gray-100/s', "</x-table>\n            </div>\n        </form>\n        <div class=\"p-4 border-t border-gray-100", $content, 1);

// Update row Tabel 2
$content = preg_replace('/(<tr class="{{ \$index % 2 == 1 \? \'bg-gray-50\/60\' : \'\' }} hover:bg-gray-100 transition-colors text-sm">)/s', 
    "$1\n                    <td class=\"px-3 py-2 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk-terbit\" value=\"{{ \$rincian->id }}\" onclick=\"toggleCheckboxTerbit()\"></td>", $content);

$content = str_replace('colspan="10"', 'colspan="11"', $content);

// 3. TABEL 3 (PROSES)
// Add "Hapus semua" button before openModalTambahProses()
$bulkBtn3 = <<<EOD
                <button type="button" id="btnModeBulkProses" onclick="toggleBulkModeProses()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>
EOD;
$content = str_replace('<x-button variant="primary" onclick="openModalTambahProses()"', $bulkBtn3 . "\n                <x-button variant=\"primary\" onclick=\"openModalTambahProses()\"", $content);

// Form and Action Bar
$actionBar3 = <<<EOD
        <form id="bulkDeleteFormProses" action="{{ route('perizinan-proses.destroyBulk') }}" method="POST" >
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulkProses" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100 relative z-10">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAllProses()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulkProses" class="hide-bulk overflow-x-auto w-full">
                <x-table :headers="['<input type=\'checkbox\' id=\'selectAllBulkProses\' onclick=\'toggleSelectAllProses()\'>', 'Tahun', 'No.', 'Perizinan Proses', 'Target', 'Periode (Bulan)', 'Aksi']">
EOD;
$content = str_replace('<x-table :headers="[\'Tahun\', \'No.\', \'Perizinan Proses\', \'Target\', \'Periode (Bulan)\', \'Aksi\']">', $actionBar3, $content);
$content = preg_replace('/<\/x-table>\s*<\/x-card>/s', "</x-table>\n            </div>\n        </form>\n    </x-card>", $content, 1);

// Update row Tabel 3
$content = preg_replace('/(<tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-200">)/s', 
    "$1\n                        <td class=\"px-3 py-2 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk-proses\" value=\"{{ \$proses->id }}\" onclick=\"toggleCheckboxProses()\"></td>", $content);

$content = str_replace('colspan="6"', 'colspan="7"', $content);

// 4. Add JS script
$js = <<<EOD
    <script>
        // TABEL 2 (TERBIT)
        function toggleBulkModeTerbit() {
            let container = document.getElementById("tableContainerBulkTerbit");
            let btn = document.getElementById("btnModeBulkTerbit");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllTerbit();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllTerbit() {
            let selectAll = document.getElementById("selectAllBulkTerbit");
            let checkboxes = document.querySelectorAll(".cb-bulk-terbit");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnTerbit();
        }
        function toggleCheckboxTerbit() {
            let selectAll = document.getElementById("selectAllBulkTerbit");
            let checkboxes = document.querySelectorAll(".cb-bulk-terbit");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnTerbit();
        }
        function toggleDeleteBtnTerbit() {
            let group = document.getElementById("btnGroupBulkTerbit");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-terbit:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllTerbit() {
            let selectAll = document.getElementById("selectAllBulkTerbit");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-terbit");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnTerbit();
        }

        // TABEL 3 (PROSES)
        function toggleBulkModeProses() {
            let container = document.getElementById("tableContainerBulkProses");
            let btn = document.getElementById("btnModeBulkProses");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllProses();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllProses() {
            let selectAll = document.getElementById("selectAllBulkProses");
            let checkboxes = document.querySelectorAll(".cb-bulk-proses");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnProses();
        }
        function toggleCheckboxProses() {
            let selectAll = document.getElementById("selectAllBulkProses");
            let checkboxes = document.querySelectorAll(".cb-bulk-proses");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnProses();
        }
        function toggleDeleteBtnProses() {
            let group = document.getElementById("btnGroupBulkProses");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-proses:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllProses() {
            let selectAll = document.getElementById("selectAllBulkProses");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-proses");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnProses();
        }
    </script>
EOD;

$endsectionPos = strrpos($content, '@endsection');
if ($endsectionPos !== false) {
    $content = substr_replace($content, "\n" . $js . "\n", $endsectionPos, 0);
}

file_put_contents('resources/views/perizinan-perkantoran.blade.php', $content);
echo "Perizinan Perkantoran view updated for bulk delete.\n";
