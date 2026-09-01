<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\bar-sk-memo.blade.php';
$content = file_get_contents($file);

// Add Checkbox to Terbit rows
$patternTerbit = '/(@forelse\(\$dataTerbit as \$row\).*?<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">)/s';
$replaceTerbit = '$1
                          <td class="px-4 py-3 text-center"><input type="checkbox" name="ids[]" class="cb-terbit" value="{{ $row->id }}" onclick="toggleCheckbox(\'terbit\')"></td>';
$content = preg_replace($patternTerbit, $replaceTerbit, $content);

// Add Checkbox to Proses rows
$patternProses = '/(@forelse\(\$dataProses as \$row\).*?<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">)/s';
$replaceProses = '$1
                          <td class="px-4 py-3 text-center"><input type="checkbox" name="ids[]" class="cb-proses" value="{{ $row->id }}" onclick="toggleCheckbox(\'proses\')"></td>';
$content = preg_replace($patternProses, $replaceProses, $content);

// In my previous script, I accidentally replaced colspan for the footer to `count($dataTerbit) > 0` for both?
// Let's check if the Proses footer uses count($dataTerbit) > 0 by mistake.
$patternFooterProses = '/@if\(count\(\$dataTerbit\) > 0\)\s*<tr class="bg-gray-100 text-gray-800 font-bold text-xs whitespace-nowrap border-t \r?\nborder-gray-200">\s*<td colspan="3"[^>]*>Total \r?\nKeseluruhan<\/td>\s*<td[^>]*>\{\{ \$grandTotalProses/s';
$replaceFooterProses = '@if(count($dataProses) > 0)
                      <tr class="bg-gray-100 text-gray-800 font-bold text-xs whitespace-nowrap border-t border-gray-200">
                          <td colspan="3" class="px-4 py-3 text-right uppercase border-r border-gray-300">Total Keseluruhan</td>
                          <td class="px-4 py-3 text-center">{{ $grandTotalProses';
$content = preg_replace($patternFooterProses, $replaceFooterProses, $content);


file_put_contents($file, $content);
echo "Fixed missing row checkboxes";
