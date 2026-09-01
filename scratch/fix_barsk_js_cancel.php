<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\bar-sk-memo.blade.php';
$content = file_get_contents($file);

// Fix the backslash errors
$content = str_replace("toggleSelectAll(\\'terbit\\')", "toggleSelectAll('terbit')", $content);
$content = str_replace("toggleSelectAll(\\'proses\\')", "toggleSelectAll('proses')", $content);

// In case single quotes were escaped differently
$content = str_replace("toggleSelectAll(\\'terbit')", "toggleSelectAll('terbit')", $content);
$content = str_replace("toggleSelectAll(\\'proses')", "toggleSelectAll('proses')", $content);

// Let's just use regex to fix any backslashes inside toggleSelectAll and toggleCheckbox
$content = preg_replace("/toggleSelectAll\(\\\\'(.*?)\\\\'\)/", "toggleSelectAll('$1')", $content);
$content = preg_replace("/toggleCheckbox\(\\\\'(.*?)\\\\'\)/", "toggleCheckbox('$1')", $content);

// Add Batal button next to Hapus Terpilih
$btnHapusTerbit = '<button type="submit" form="bulkDeleteTerbitForm" id="btnHapusTerbit" class="hidden px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors">Hapus Terpilih</button>';
$btnBatalTerbit = '<div id="btnGroupTerbit" class="hidden flex gap-2"><button type="button" onclick="cancelAll(\'terbit\')" class="px-3 py-1.5 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition-colors">Batal</button><button type="submit" form="bulkDeleteTerbitForm" class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors">Hapus Terpilih</button></div>';
$content = str_replace($btnHapusTerbit, $btnBatalTerbit, $content);

$btnHapusProses = '<button type="submit" form="bulkDeleteProsesForm" id="btnHapusProses" class="hidden px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors">Hapus Terpilih</button>';
$btnBatalProses = '<div id="btnGroupProses" class="hidden flex gap-2"><button type="button" onclick="cancelAll(\'proses\')" class="px-3 py-1.5 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition-colors">Batal</button><button type="submit" form="bulkDeleteProsesForm" class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors">Hapus Terpilih</button></div>';
$content = str_replace($btnHapusProses, $btnBatalProses, $content);

// Update Javascript
$jsOld = 'function toggleDeleteBtn(tipe) {
    let btn = document.getElementById("btnHapus" + (tipe === "terbit" ? "Terbit" : "Proses"));
    let checked = document.querySelectorAll(".cb-" + tipe + ":checked").length > 0;
    if(checked) btn.classList.remove("hidden"); else btn.classList.add("hidden");
}';

$jsNew = 'function toggleDeleteBtn(tipe) {
    let group = document.getElementById("btnGroup" + (tipe === "terbit" ? "Terbit" : "Proses"));
    if(group) {
        let checked = document.querySelectorAll(".cb-" + tipe + ":checked").length > 0;
        if(checked) group.classList.remove("hidden"); else group.classList.add("hidden");
    }
}
function cancelAll(tipe) {
    let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
    if(selectAll) selectAll.checked = false;
    let checkboxes = document.querySelectorAll(".cb-" + tipe);
    checkboxes.forEach(cb => cb.checked = false);
    toggleDeleteBtn(tipe);
}';

$content = str_replace($jsOld, $jsNew, $content);

file_put_contents($file, $content);
echo "Fixed JS and added cancel button.";
