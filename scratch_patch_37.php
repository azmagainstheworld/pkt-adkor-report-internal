<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// Add hidden inputs to Terbit Form
$formTerbitSearch = "<form id=\"bulkDeleteFormTerbit\" action=\"{{ route('perizinan-perkantoran.destroyBulk') }}\" method=\"POST\" >
            @csrf
            @method('DELETE')";
$formTerbitReplace = $formTerbitSearch . "\n            <input type=\"hidden\" name=\"filter_tahun\" value=\"{{ \$filterTahun }}\">\n            <input type=\"hidden\" name=\"filter_bulan\" value=\"{{ \$filterBulan }}\">";
if (strpos($content, '<input type="hidden" name="filter_tahun"') === false) {
    $content = str_replace($formTerbitSearch, $formTerbitReplace, $content);
}

// Add hidden inputs to Proses Form
$formProsesSearch = "<form id=\"bulkDeleteFormProses\" action=\"{{ route('perizinan-proses.destroyBulk') }}\" method=\"POST\" >
            @csrf
            @method('DELETE')";
$formProsesReplace = $formProsesSearch . "\n            <input type=\"hidden\" name=\"filter_tahun\" value=\"{{ \$filterTahun }}\">";
if (strpos($content, $formProsesSearch) !== false && strpos($content, $formProsesReplace) === false) {
    $content = str_replace($formProsesSearch, $formProsesReplace, $content);
}

// Add buttons
$btnSearch = '<button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>';
$btnTerbitReplace = '<button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>' . "\n                    " . '<button type="submit" name="delete_all_pages" value="1" id="btnDeleteAllPagesTerbit" class="hidden px-3 py-1.5 bg-red-800 text-white rounded-lg text-xs font-medium hover:bg-red-900">Hapus Seluruh Halaman</button>';
$btnProsesReplace = '<button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>' . "\n                    " . '<button type="submit" name="delete_all_pages" value="1" id="btnDeleteAllPagesProses" class="hidden px-3 py-1.5 bg-red-800 text-white rounded-lg text-xs font-medium hover:bg-red-900">Hapus Seluruh Halaman</button>';

// Because str_replace replaces all instances, and we have 2 instances of $btnSearch, we can replace them sequentially or with regex.
// Since we want different IDs, we use regex with limit 1
$content = preg_replace('/' . preg_quote($btnSearch, '/') . '/', $btnTerbitReplace, $content, 1);
$content = preg_replace('/' . preg_quote($btnSearch, '/') . '/', $btnProsesReplace, $content, 1);

// Now update Javascript
$jsTerbitSearch = <<<EOD
        function toggleDeleteBtnTerbit() {
            let group = document.getElementById("btnGroupBulkTerbit");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-terbit:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;
$jsTerbitReplace = <<<EOD
        function toggleDeleteBtnTerbit() {
            let group = document.getElementById("btnGroupBulkTerbit");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-terbit:checked").length > 0;
                let selectAll = document.getElementById("selectAllBulkTerbit");
                let btnAllPages = document.getElementById("btnDeleteAllPagesTerbit");
                if (checked) { 
                    group.classList.remove("hidden"); 
                    if (selectAll && selectAll.checked && btnAllPages) { btnAllPages.classList.remove("hidden"); }
                    else if (btnAllPages) { btnAllPages.classList.add("hidden"); }
                } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;
$content = str_replace($jsTerbitSearch, $jsTerbitReplace, $content);

$jsProsesSearch = <<<EOD
        function toggleDeleteBtnProses() {
            let group = document.getElementById("btnGroupBulkProses");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-proses:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;
$jsProsesReplace = <<<EOD
        function toggleDeleteBtnProses() {
            let group = document.getElementById("btnGroupBulkProses");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-proses:checked").length > 0;
                let selectAll = document.getElementById("selectAllBulkProses");
                let btnAllPages = document.getElementById("btnDeleteAllPagesProses");
                if (checked) { 
                    group.classList.remove("hidden");
                    if (selectAll && selectAll.checked && btnAllPages) { btnAllPages.classList.remove("hidden"); }
                    else if (btnAllPages) { btnAllPages.classList.add("hidden"); }
                } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;
$content = str_replace($jsProsesSearch, $jsProsesReplace, $content);

file_put_contents($path, $content);
echo "Blade updated\n";
?>
