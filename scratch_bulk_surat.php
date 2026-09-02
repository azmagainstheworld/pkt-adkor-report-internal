<?php
$content = file_get_contents('resources/views/surat-masuk-keluar.blade.php');

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

// 2. Add "Mode Hapus Massal" button next to "Catat Surat Satuan"
$bulkBtn = <<<EOD
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>
EOD;
$content = str_replace('<button type="button" onclick="openModalTambah()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga', $bulkBtn . "\n                <button type=\"button\" onclick=\"openModalTambah()\" class=\"inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga", $content);

// 3. Form and Action Bar
$actionBar = <<<EOD
        <form id="bulkDeleteForm" action="{{ route('surat.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
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

// Replace exactly where Table 2's overflow-x-auto starts
$content = preg_replace('/<div class="overflow-x-auto">\s*<table class="w-full text-sm text-left text-gray-600">\s*<thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">\s*<tr>/s', $actionBar . "\n            <table class=\"w-full text-sm text-left text-gray-600\">\n                <thead class=\"text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100\">\n                    <tr>", $content, 1);

// Close Form
$content = preg_replace('/<\/table>\s*<\/div>\s*<\/x-card>/s', "</table>\n            </div>\n        </form>\n    </x-card>", $content, 1);

// 4. Update array headers for Tabel 2
$content = str_replace("\$headers = ['No', 'Tahun', 'Bulan', 'Nomor Surat'", "\$headers = ['<input type=\"checkbox\" id=\"selectAllBulk\" onclick=\"toggleSelectAll()\">', 'No', 'Tahun', 'Bulan', 'Nomor Surat'", $content);

// Update HTML checkbox td
$content = preg_replace('/(<tr class="hover:bg-gray-50 transition-colors text-xs">\s*<td class="px-6 py-4 text-gray-500 font-medium">{{ \$index \+ 1 }})/s', 
    "<tr class=\"hover:bg-gray-50 transition-colors text-xs\">\n                            <td class=\"px-6 py-4 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk\" value=\"{{ \$row->id }}\" onclick=\"toggleCheckbox()\"></td>\n                            <td class=\"px-6 py-4 text-gray-500 font-medium\">{{ \$index + 1 }}", $content);

// Update colspan
$content = preg_replace('/(<tr><td colspan="{{ count\(\$headers\) )("\s*class="px-6 py-10 text-center text-gray-500">)/', '${1}${2}', $content); // colspan dynamic

// 5. Add JS script
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

// IMPORTANT: In PHP < 7.4 inside blade, sometimes {!! $header !!} is needed to render HTML in header array properly!
// Let's replace {{ $header }} with {!! $header !!} in Table 2 header row.
$content = preg_replace('/(<th class="px-6 py-3.5 font-semibold whitespace-nowrap">)\{\{ \$header \}\}(<\/th>)/s', '$1{!! $header !!}$2', $content);

file_put_contents('resources/views/surat-masuk-keluar.blade.php', $content);
echo "Surat Masuk Keluar view updated for bulk delete.\n";
