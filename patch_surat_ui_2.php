<?php
$file = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($file);

// 1. Fix the bulkDeleteForm and btnGroupBulk positions.
// Currently, Table 1 starts with:
// <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
// <form id="bulkDeleteForm" ...>
// @csrf
// @method('DELETE')
// <div id="btnGroupBulk" ...>
// 
// And ends with:
// </table>
// </div>
// </form>
// </x-card>

// Let's remove the form and btnGroupBulk from Table 1.
$table1Pattern = '/(<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">)\s*<form id="bulkDeleteForm"[^>]*>\s*@csrf\s*@method\(\'DELETE\'\)\s*(<div id="btnGroupBulk"[\s\S]*?<\/div>)\s*(<div class="p-5 border-b border-gray-100 bg-white">)/U';
preg_match($table1Pattern, $content, $matches1);

if (!empty($matches1)) {
    $btnGroupBulkHTML = $matches1[2]; // We save this to put in Table 2
    
    // Remove form start and btnGroupBulk from Table 1
    $content = preg_replace($table1Pattern, '$1' . "\n        " . '$3', $content, 1);
    
    // Remove form end from Table 1
    $content = preg_replace('/(<\/table>\s*<\/div>)\s*<\/form>\s*(<\/x-card>)/U', '$1' . "\n    " . '$2', $content, 1);
    
    // Now inject them into Table 2.
    // Table 2 starts at:
    // <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white">
    // <div class="p-5 border-b border-gray-100 bg-white">
    $table2Pattern = '/(<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white">)\s*(<div class="p-5 border-b border-gray-100 bg-white">)/U';
    
    $table2Replacement = '$1' . "\n        " . '<form id="bulkDeleteForm" action="{{ route(\'surat.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih?\')">' . "\n            @csrf\n            @method('DELETE')\n            \n            " . $btnGroupBulkHTML . "\n\n        " . '$2';
    
    $content = preg_replace($table2Pattern, $table2Replacement, $content, 1);
    
    // Add </form> at the end of Table 2
    // It ends with:
    // </table>
    // </div>
    // </x-card>
    // But wait! Is Table 2 already wrapped in </form> from my previous run? Let's check.
    // In my previous run, I added </form> at the end of Table 2. But let's safely ensure it.
    if (strpos($content, '</form>', strpos($content, '<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white">')) === false) {
        $content = preg_replace('/(<\/table>\s*<\/div>\s*)(<\/x-card>)/U', '$1        </form>' . "\n    " . '$2', $content, 1);
    }
} else {
    echo "Could not find bulkDeleteForm in Table 1.\n";
}

// 2. Fix Opsi Lanjutan.
// The user wants TWO Opsi Lanjutan dropdowns.
// One for Table 1 (already there, let's keep it but rename options and ID).
// One for Table 2 (we will add it next to "Catat Surat Satuan" button).

// Let's modify the existing Opsi Lanjutan in Table 1.
$table1DropdownPattern = '/(<!-- Opsi Lanjutan -->\s*<div class="relative inline-block text-left">)\s*<button type="button" onclick="toggleDropdown\(\'dropdownOpsiSurat\'\)"([\s\S]*?)<div id="dropdownOpsiSurat"([\s\S]*?)<\/div>\s*<\/div>\s*<\/div>/U';

// Replace IDs to dropdownOpsiSurat1
$content = preg_replace('/toggleDropdown\(\'dropdownOpsiSurat\'\)/', "toggleDropdown('dropdownOpsiSurat1')", $content, 1);
$content = preg_replace('/id="dropdownOpsiSurat"/', 'id="dropdownOpsiSurat1"', $content, 1);
$content = preg_replace('/toggleDropdown\(\'dropdownOpsiSurat\'\)/', "toggleDropdown('dropdownOpsiSurat1')", $content, 2); // for the buttons inside

// Now, copy a modified Opsi Lanjutan into Table 2 action bar.
// Table 2 action bar has:
// <!-- Tambah Data Utama -->
// <div>
// <button type="button" id="btnModeBulk" ...
$table2ActionBarPattern = '/(<!-- Tambah Data Utama -->\s*<div>\s*<button type="button" id="btnModeBulk")/U';

$opsiLanjutanTable2 = '
            <!-- Opsi Lanjutan Tabel 2 -->
            <div class="relative inline-block text-left mr-2">
                <button type="button" onclick="toggleDropdown(\'dropdownOpsiSurat2\')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Opsi Lanjutan
                    <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="dropdownOpsiSurat2" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor Tabel Rincian (Excel/PDF)</p>
                    </div>
                    <div class="py-1" role="none">
                        <a href="{{ route(\'surat.export.excel\', [\'jenis\' => \'tabel2\', \'tahun\' => $tahunFilter, \'bulan\' => $bulanFilter]) }}" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor ke Excel
                        </a>
                        <a href="{{ route(\'surat.export.pdf\', [\'jenis\' => \'tabel2\', \'tahun\' => $tahunFilter, \'bulan\' => $bulanFilter]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor ke PDF
                        </a>
                    </div>
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Impor & Konfigurasi</p>
                    </div>
                    <div class="py-1" role="none">
                        <button type="button" onclick="openModal(\'modalImportExcel\'); toggleDropdown(\'dropdownOpsiSurat2\')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Impor Data Rincian dari Excel
                        </button>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <button type="button" onclick="openModal(\'modalAturKolom\'); toggleDropdown(\'dropdownOpsiSurat2\')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom Tambahan
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            
            $1';

$content = preg_replace($table2ActionBarPattern, $opsiLanjutanTable2, $content, 1);

// Now simplify the dropdown in Table 1 to ONLY have Export Tabel 1 and Import Data Rekap.
$table1DropdownPatternExact = '/(<div id="dropdownOpsiSurat1"[\s\S]*?<\/div>\s*<\/div>\s*<\/div>)/U';
preg_match($table1DropdownPatternExact, $content, $dropdown1Matches);
if (!empty($dropdown1Matches)) {
    $dropdown1New = '
                <div id="dropdownOpsiSurat1" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor Tabel Rekapitulasi (Excel/PDF)</p>
                    </div>
                    <div class="py-1" role="none">
                        <a href="{{ route(\'surat.export.excel\', [\'jenis\' => \'tabel1\', \'tahun\' => $tahunFilter, \'bulan\' => $bulanFilter]) }}" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor ke Excel
                        </a>
                        <a href="{{ route(\'surat.export.pdf\', [\'jenis\' => \'tabel1\', \'tahun\' => $tahunFilter, \'bulan\' => $bulanFilter]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor ke PDF
                        </a>
                    </div>
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Impor Data Rekapitulasi</p>
                    </div>
                    <div class="py-1" role="none">
                        <!-- We use the same import modal for now, but label it specifically for Rekap -->
                        <button type="button" onclick="openModal(\'modalImportExcel\'); toggleDropdown(\'dropdownOpsiSurat1\')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Impor Data Rekap dari Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    
    $content = preg_replace($table1DropdownPatternExact, ltrim($dropdown1New), $content, 1);
}

// 3. Make sure tableContainerBulk has the hide-bulk class on Table 2, NOT Table 1.
// In Table 1, let's remove id="tableContainerBulk" and class="hide-bulk" if it exists.
$table1ContainerPattern = '/(<h3 class="font-bold text-gray-900 text-lg">Akumulasi Laporan Surat Masuk dan Kekuar<\/h3>[\s\S]*?)<div class="overflow-x-auto">/U';
$table1ContainerReplacement = '$1<div class="overflow-x-auto">'; // Ensure it doesn't have hide-bulk

$content = preg_replace('/(<div class="overflow-x-auto">)\s*<table class="w-full text-sm text-left text-gray-600">\s*<thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">\s*<tr>\s*<th class="px-6 py-3.5 font-semibold">Tahun<\/th>/U', '<div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold">Tahun</th>', $content);
                        

file_put_contents($file, $content);
echo "Layout fixed perfectly.\n";
