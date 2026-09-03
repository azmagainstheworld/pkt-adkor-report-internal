<?php
$file = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($file);

// 1. Fix SyntaxError `<script>` stray tag.
// Found around function cancelAll()
$strayScriptPattern = "        }\n\n<script>\n    function openModal";
$strayScriptFix = "        }\n\n    function openModal";
$content = str_replace($strayScriptPattern, $strayScriptFix, $content);


// 2. Remove "Tabel 1" and "Tabel 2" titles
$content = str_replace('Tabel 2: Arsip Detail Surat Satuan', 'Arsip Detail Surat Satuan', $content);
$content = str_replace('Tabel 1: Akumulasi Laporan', 'Akumulasi Laporan', $content); // In case it has it, though it says Akumulasi Laporan Surat Masuk dan Kekuar


// 3. Extract and Remove the Action Bar from outside Table 2.
// The Action Bar in the original file looks like this:
$actionBarStart = '<!-- ================= Req 4: ACTION BAR DIPINDAH KE ATAS TABEL 2 ================= -->';
$actionBarEnd = '<!-- TABEL 2 (DETAIL DATA SATUAN) -->';

$startPos = strpos($content, $actionBarStart);
$endPos = strpos($content, $actionBarEnd);

if ($startPos !== false && $endPos !== false) {
    // We remove it entirely. We will rebuild the dropdowns from scratch to ensure perfection.
    $content = substr_replace($content, '', $startPos, $endPos - $startPos);
}

// 4. Remove the `bulkDeleteForm` and `btnGroupBulk` from Table 1.
// Table 1 starts with:
$t1Start = '<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Akumulasi Laporan Surat Masuk dan Kekuar</h3>
            <p class="text-xs text-gray-400">Total surat berstatus <span class="text-green-600 font-medium">"Terkirim"</span> terfilter Tahun: {{ $tahunFilter }}, Bulan: {{ $bulanFilter }}</p>
        </div>

                <form id="bulkDeleteForm" action="{{ route(\'surat.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih?\')">
            @csrf
            @method(\'DELETE\')
            
            <div id="btnGroupBulk" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulk" class="hide-bulk overflow-x-auto">';

$t1Fix = '<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Akumulasi Laporan Surat Masuk dan Kekuar</h3>
                    <p class="text-xs text-gray-400">Total surat berstatus <span class="text-green-600 font-medium">"Terkirim"</span> terfilter Tahun: {{ $tahunFilter }}, Bulan: {{ $bulanFilter }}</p>
                </div>
                <!-- Opsi Lanjutan Table 1 -->
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleDropdown(\'dropdownOpsiSurat1\')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
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
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">';

$content = str_replace($t1Start, $t1Fix, $content);

// Remove the `</form>` at the end of Table 1.
$t1End = '            </table>
            </div>
        </form>
    </x-card>';
$t1EndFix = '            </table>
        </div>
    </x-card>';
$content = str_replace($t1End, $t1EndFix, $content);


// 5. Build Table 2's Header with the `btnGroupBulk` and `bulkDeleteForm` injected correctly.
$t2Start = '    <!-- TABEL 2 (DETAIL DATA SATUAN) -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Arsip Detail Surat Satuan</h3>
            <p class="text-xs text-gray-400">Pencatatan satuan setiap berkas surat masuk dan keluar</p>
        </div>

        @php
            // Definisi Header (Req 5: Kolom File Dihapus)
            $headers = [\'<input type="checkbox" id="selectAllBulk" onclick="toggleSelectAll()">\', \'No\', \'Tahun\', \'Bulan\', \'Nomor Surat\', \'Tanggal Surat\', \'Judul Surat\', \'Status\', \'Jenis Surat\'];
            if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $headers[] = $k->nama_kolom; } }
            $headers[] = \'Aksi\';
        @endphp

        <div class="overflow-x-auto">';

$t2Fix = '    <!-- TABEL 2 (DETAIL DATA SATUAN) -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white">
        <form id="bulkDeleteForm" action="{{ route(\'surat.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih?\')">
            @csrf
            @method(\'DELETE\')
            
            <div id="btnGroupBulk" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

        <div class="p-5 border-b border-gray-100 bg-white">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Arsip Detail Surat Satuan</h3>
                    <p class="text-xs text-gray-400">Pencatatan satuan setiap berkas surat masuk dan keluar</p>
                </div>
                
                <div class="flex flex-row items-center gap-2">
                    <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus semua
                    </button>
                    
                    <div class="relative inline-block text-left">
                        <button type="button" onclick="toggleDropdown(\'dropdownOpsiSurat2\')" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-gray-200">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            Opsi Lanjutan
                            <svg class="w-3 h-3 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="dropdownOpsiSurat2" class="hidden absolute right-0 z-[50] mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                            <div class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor Tabel Rincian</p>
                            </div>
                            <div class="py-1" role="none">
                                <a href="{{ route(\'surat.export.excel\', [\'jenis\' => \'tabel2\', \'tahun\' => $tahunFilter, \'bulan\' => $bulanFilter]) }}" class="w-full text-left text-gray-700 px-3 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor ke Excel
                                </a>
                                <a href="{{ route(\'surat.export.pdf\', [\'jenis\' => \'tabel2\', \'tahun\' => $tahunFilter, \'bulan\' => $bulanFilter]) }}" target="_blank" class="w-full text-left text-gray-700 px-3 py-2 text-xs hover:bg-red-50 flex items-center gap-2 font-medium transition-colors border-t border-gray-50">
                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor ke PDF
                                </a>
                            </div>
                            <div class="px-3 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Impor & Konfigurasi</p>
                            </div>
                            <div class="py-1" role="none">
                                <button type="button" onclick="openModal(\'modalImportExcel\'); toggleDropdown(\'dropdownOpsiSurat2\')" class="w-full text-left text-gray-700 px-3 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Impor Data Excel
                                </button>
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <button type="button" onclick="openModal(\'modalAturKolom\'); toggleDropdown(\'dropdownOpsiSurat2\')" class="text-gray-700 w-full text-left px-3 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium transition-colors border-t border-gray-50">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" onclick="openModalTambah()" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-white bg-pkt-jingga hover:bg-orange-600 rounded-lg shadow-sm transition-colors border-none outline-none focus:ring-2 focus:ring-orange-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Catat Surat Satuan
                    </button>
                </div>
            </div>
        </div>

        @php
            // Definisi Header (Req 5: Kolom File Dihapus)
            $headers = [\'<input type="checkbox" id="selectAllBulk" onclick="toggleSelectAll()">\', \'No\', \'Tahun\', \'Bulan\', \'Nomor Surat\', \'Tanggal Surat\', \'Judul Surat\', \'Status\', \'Jenis Surat\'];
            if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $headers[] = $k->nama_kolom; } }
            $headers[] = \'Aksi\';
        @endphp

        <div id="tableContainerBulk" class="hide-bulk overflow-x-auto">';

$content = str_replace($t2Start, $t2Fix, $content);

// And we must close the form for Table 2!
// Table 2 ends at:
$t2EndPattern = '/(<\/table>\s*<\/div>\s*)(<\/x-card>\s*<x-delete-modal id="modalHapusSurat")/';
$content = preg_replace($t2EndPattern, '$1</form>' . "\n    " . '$2', $content, 1);


// 6. Remove the old "Unduh Template Excel" link from Modal Tambah
$modalTambahTemplateOld = '<div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="{{ route(\'template.download\', \'surat\') }}" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>';
$content = str_replace($modalTambahTemplateOld, "", $content);

file_put_contents($file, $content);
echo "Rebuild successful!\n";
