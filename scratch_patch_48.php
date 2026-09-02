<?php
$path = 'resources/views/pelaporan.blade.php';
$content = file_get_contents($path);

// The problem: Tabel 1 (Ringkasan) was accidentally wrapped in a bulkDeleteForm and tableContainerBulk.
// This causes duplicate IDs and hides the first column of Tabel 1 due to the .hide-bulk class.
// We need to strip out these wrappers from Tabel 1.

$search = <<<HTML
                <form id="bulkDeleteForm" action="{{ route('pelaporan.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
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
            <x-table :headers="['Tahun', 'Bulan', 'Tujuan Eksternal', 'Tujuan Internal', 'Total Laporan']">
HTML;

$replace = <<<HTML
            <div class="overflow-x-auto">
            <x-table :headers="['Tahun', 'Bulan', 'Tujuan Eksternal', 'Tujuan Internal', 'Total Laporan']">
HTML;

$search2 = <<<HTML
            </x-table>
            </div>
        </form>
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
HTML;

$replace2 = <<<HTML
            </x-table>
            </div>
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
HTML;

$search = str_replace("\r\n", "\n", $search);
$replace = str_replace("\r\n", "\n", $replace);
$search2 = str_replace("\r\n", "\n", $search2);
$replace2 = str_replace("\r\n", "\n", $replace2);
$content = str_replace("\r\n", "\n", $content);

$content = str_replace($search, $replace, $content);
$content = str_replace($search2, $replace2, $content);

// Ensure CSS exists
if (strpos($content, '.hide-bulk') === false) {
    $css = <<<HTML
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>
HTML;
    $content = str_replace('@section(\'content\')', "@section('content')\n" . $css, $content);
}

file_put_contents($path, $content);
echo "Tabel 1 wrapper removed!\n";
?>
