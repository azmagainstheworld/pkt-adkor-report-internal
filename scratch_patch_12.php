<?php

$path = 'resources/views/ketidakhadiran/index.blade.php';
$content = file_get_contents($path);

// 1. Fix preg_replace backreference (\$1 -> $1)
$content = str_replace('">\$1</mark>\'', '">$1</mark>\'', $content);

// 2. Wrap Atur Kolom section with @if(auth()->user()->isAdmin())
$aturKolomSection = <<<HTML
                <!-- Kategori 2: Atur Kolom -->
                <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                </div>
                <div class="py-1" role="none">
                    <button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom
                    </button>
                </div>
HTML;
if (strpos($content, $aturKolomSection) !== false && strpos($content, '@if(auth()->user()->isAdmin())' . "\n" . '                <!-- Kategori 2: Atur Kolom -->') === false) {
    $content = str_replace($aturKolomSection, "@if(auth()->user()->isAdmin())\n" . $aturKolomSection . "\n                @endif", $content);
}

// 3. Implement Bulk Delete (CSS + Table Headers + Checkboxes + JS)
// CSS
$cssBlock = <<<HTML
<style>
    .hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>
</main>
HTML;
if (strpos($content, '.hide-bulk th:first-child') === false) {
    $content = str_replace('</main>', $cssBlock, $content);
}

// Table Header
$oldHeader = "<x-table :headers=\"['Tahun', 'Bulan', 'Nama', 'NPK', 'Keterangan', 'Dinas', 'Cuti', 'Izin', 'Training', 'Dispensasi', 'Detasering', 'Aksi']\">";
$newHeader = "<x-table :headers=\"['<input type=\'checkbox\' id=\'selectAll\' onclick=\'toggleSelectAll()\'>', 'Tahun', 'Bulan', 'Nama', 'NPK', 'Keterangan', 'Dinas', 'Cuti', 'Izin', 'Training', 'Dispensasi', 'Detasering', 'Aksi']\">";
$content = str_replace($oldHeader, $newHeader, $content);

// Table Row Checkbox
$oldTr = "<tr class=\"row-ketidakhadiran hover:bg-gray-50 transition-colors cursor-pointer\" data-nama=\"{{ \$item->nama }}\" data-dinas=\"{{ \$item->dinas ?? 0 }}\" data-cuti=\"{{ \$item->cuti ?? 0 }}\" data-izin=\"{{ \$item->izin ?? 0 }}\" data-training=\"{{ \$item->training ?? 0 }}\" data-dispensasi=\"{{ \$item->dispensasi ?? 0 }}\" data-detasering=\"{{ \$item->detasering ?? 0 }}\">";
$newTr = $oldTr . "\n                    <td class=\"px-6 py-4 text-center\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk\" value=\"{{ \$item->ketidakhadiran_id }}\" onclick=\"toggleCheckbox()\"></td>";
if (strpos($content, 'class="cb-bulk"') === false) {
    $content = str_replace($oldTr, $newTr, $content);
}

// JS Bulk Delete Functions
$jsBulkFunctions = <<<JS
function toggleBulkMode() {
    let container = document.getElementById("tableContainer");
    if (container.classList.contains("hide-bulk")) {
        container.classList.remove("hide-bulk");
        document.getElementById("btnModeBulk").innerHTML = "Batal Hapus Massal";
    } else {
        container.classList.add("hide-bulk");
        document.getElementById("btnModeBulk").innerHTML = "Hapus Semua";
        cancelAll();
    }
}

function toggleSelectAll() {
    let selectAll = document.getElementById("selectAll");
    let checkboxes = document.querySelectorAll(".cb-bulk");
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    toggleDeleteBtn();
}

function toggleCheckbox() {
    let selectAll = document.getElementById("selectAll");
    let checkboxes = document.querySelectorAll(".cb-bulk");
    selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
    toggleDeleteBtn();
}

function toggleDeleteBtn() {
    let group = document.getElementById("btnGroup");
    let checkedCount = document.querySelectorAll(".cb-bulk:checked").length;
    if (checkedCount > 0) {
        group.classList.remove("hidden");
        group.classList.add("flex");
        document.getElementById("selectedCount").innerText = checkedCount;
    } else {
        group.classList.add("hidden");
        group.classList.remove("flex");
    }
}

function cancelAll() {
    let selectAll = document.getElementById("selectAll");
    if (selectAll) selectAll.checked = false;
    let checkboxes = document.querySelectorAll(".cb-bulk");
    checkboxes.forEach(cb => cb.checked = false);
    toggleDeleteBtn();
}
JS;

// Replace existing toggleBulkMode (if any) or just append
$oldToggleBulk = <<<JS
function toggleBulkMode() {
      let container = document.getElementById("tableContainer");
      if (container.classList.contains("hide-bulk")) {
          container.classList.remove("hide-bulk");
JS;

if (strpos($content, 'function toggleBulkMode()') !== false) {
    // Just find and replace everything after function toggleBulkMode to the end of that script or something...
    // Actually, I can just preg_replace the entire toggleBulkMode function if it exists.
    $content = preg_replace('/function toggleBulkMode\(\) \{[\s\S]*?(?=function|<\/script>)/', $jsBulkFunctions . "\n\n", $content);
} else {
    // Append to end of script
    $content = str_replace('</script>', $jsBulkFunctions . "\n</script>", $content);
}

// Rename 'Mode Hapus Massal' to 'Hapus Semua' in the button
$content = str_replace('Mode Hapus Massal', 'Hapus Semua', $content);

file_put_contents($path, $content);

echo "Applied fixes to ketidakhadiran/index.blade.php\n";

?>
