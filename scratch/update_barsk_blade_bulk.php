<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\bar-sk-memo.blade.php';
$content = file_get_contents($file);

// 1. Add Select All Checkbox to Headers
$content = str_replace(
    "\$headTerbit = ['Tahun',", 
    "\$headTerbit = ['<input type=\"checkbox\" id=\"selectAllTerbit\" onclick=\"toggleSelectAll(\'terbit\')\">', 'Tahun',", 
    $content
);
$content = str_replace(
    "\$headProses = ['Tahun',", 
    "\$headProses = ['<input type=\"checkbox\" id=\"selectAllProses\" onclick=\"toggleSelectAll(\'proses\')\">', 'Tahun',", 
    $content
);

// 2. Add Hapus Terpilih Button and Form for Terbit
$terbitFormStart = '
              <div class="flex justify-between items-center mb-2">
                  <h3 class="font-bold text-gray-700"></h3>
                  <button type="submit" form="bulkDeleteTerbitForm" id="btnHapusTerbit" class="hidden px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors">Hapus Terpilih</button>
              </div>
              <form id="bulkDeleteTerbitForm" action="{{ route(\'bar-sk-memo.destroyTerbit\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih?\')">
                  @csrf @method(\'DELETE\')
              <x-table :headers="$headTerbit">';
$content = preg_replace('/<x-table :headers="\$headTerbit">/', $terbitFormStart, $content, 1);

// Close form for Terbit
$terbitFormEnd = '
              </x-table>
              </form>';
// Replace the first </x-table> only
$content = preg_replace('/<\/x-table>/', $terbitFormEnd, $content, 1);

// 3. Add Individual Checkboxes in Terbit Row
$terbitRowStart = '<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-center"><input type="checkbox" name="ids[]" class="cb-terbit" value="{{ $row->id }}" onclick="toggleCheckbox(\'terbit\')"></td>';
// We need to replace the <tr> inside the Terbit loop only.
// Let's replace @forelse($dataTerbit as $row) \n <tr...>
$patternTerbitTr = '/@forelse\(\$dataTerbit as \$row\)\s*<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">/';
$replaceTerbitTr = '@forelse($dataTerbit as $row)
                    ' . $terbitRowStart;
$content = preg_replace($patternTerbitTr, $replaceTerbitTr, $content);


// 4. Add Hapus Terpilih Button and Form for Proses
$prosesFormStart = '
              <div class="flex justify-between items-center mb-2 mt-8">
                  <h3 class="font-bold text-gray-700"></h3>
                  <button type="submit" form="bulkDeleteProsesForm" id="btnHapusProses" class="hidden px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors">Hapus Terpilih</button>
              </div>
              <form id="bulkDeleteProsesForm" action="{{ route(\'bar-sk-memo.destroyProses\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih?\')">
                  @csrf @method(\'DELETE\')
              <x-table :headers="$headProses">';
$content = preg_replace('/<x-table :headers="\$headProses">/', $prosesFormStart, $content, 1);

// Close form for Proses
$prosesFormEnd = '
              </x-table>
              </form>';
// Replace the next </x-table> (which is the last one)
$content = preg_replace('/<\/x-table>/', $prosesFormEnd, $content, 1);

// 5. Add Individual Checkboxes in Proses Row
$prosesRowStart = '<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-center"><input type="checkbox" name="ids[]" class="cb-proses" value="{{ $row->id }}" onclick="toggleCheckbox(\'proses\')"></td>';
$patternProsesTr = '/@forelse\(\$dataProses as \$row\)\s*<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">/';
$replaceProsesTr = '@forelse($dataProses as $row)
                    ' . $prosesRowStart;
$content = preg_replace($patternProsesTr, $replaceProsesTr, $content);

// 6. Increase colspan in empty state and footer
$content = preg_replace('/colspan="\{\{\scount\(\$headTerbit\)\s\}\}"/', 'colspan="{{ count($headTerbit) + 1 }}"', $content);
$content = preg_replace('/colspan="\{\{\scount\(\$headProses\)\s\}\}"/', 'colspan="{{ count($headProses) + 1 }}"', $content);
// And the total row colspan="2" should become colspan="3"
$content = preg_replace('/<td colspan="2" class="px-4 py-3 text-right uppercase border-r border-gray-300">Total Keseluruhan<\/td>/', '<td colspan="3" class="px-4 py-3 text-right uppercase border-r border-gray-300">Total Keseluruhan</td>', $content);

// 7. Add JS Script at the end
$js = '
<script>
function toggleSelectAll(tipe) {
    let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses")).checked;
    let checkboxes = document.querySelectorAll(".cb-" + tipe);
    checkboxes.forEach(cb => cb.checked = selectAll);
    toggleDeleteBtn(tipe);
}
function toggleCheckbox(tipe) {
    let allChecked = true;
    let checkboxes = document.querySelectorAll(".cb-" + tipe);
    checkboxes.forEach(cb => { if(!cb.checked) allChecked = false; });
    document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses")).checked = allChecked;
    toggleDeleteBtn(tipe);
}
function toggleDeleteBtn(tipe) {
    let btn = document.getElementById("btnHapus" + (tipe === "terbit" ? "Terbit" : "Proses"));
    let checked = document.querySelectorAll(".cb-" + tipe + ":checked").length > 0;
    if(checked) btn.classList.remove("hidden"); else btn.classList.add("hidden");
}
</script>
';
$content = str_replace('</x-layout>', $js . '</x-layout>', $content);

file_put_contents($file, $content);
echo "Blade view updated for bulk delete";
