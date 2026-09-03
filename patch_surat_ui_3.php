<?php
$file = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($file);

// Remove "Impor Data Rekapitulasi" section from Table 1 dropdown
$dropdown1ImportPattern = '/<div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">\s*<p class="text-\[10px\] font-bold text-gray-500 uppercase tracking-wider">Impor Data Rekapitulasi<\/p>\s*<\/div>\s*<div class="py-1" role="none">\s*<!-- We use the same import modal for now, but label it specifically for Rekap -->\s*<button type="button" onclick="openModal\(\'modalImportExcel\'\); toggleDropdown\(\'dropdownOpsiSurat1\'\)" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">\s*<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"><\/path><\/svg> Impor Data Rekap dari Excel\s*<\/button>\s*<\/div>/is';

$content = preg_replace($dropdown1ImportPattern, '', $content);

file_put_contents($file, $content);
echo "Table 1 dropdown fixed to only have Exports.\n";
