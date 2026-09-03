<?php
$file = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($file);

// 1. Move the form and btnGroupBulk from Table 1 to Table 2.
$formStart = '<form id="bulkDeleteForm" action="{{ route(\'surat.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih?\')">';
$formCsrf = "@csrf\n            @method('DELETE')";
$btnGroupBulk = <<<HTML
<div id="btnGroupBulk" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>
HTML;

// Remove them from Table 1
$content = preg_replace('/<form id="bulkDeleteForm"[^>]*>\s*@csrf\s*@method\(\'DELETE\'\)\s*<div id="btnGroupBulk"[\s\S]*?<\/div>\s*<\/div>/', '', $content, 1);
$content = str_replace('<div id="tableContainerBulk" class="hide-bulk overflow-x-auto">', '<div class="overflow-x-auto">', $content);
$content = str_replace("</form>\n    </x-card>", "</x-card>", $content);

// Wrap Table 2 with them
$table2Start = '<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white">';
$table2StartReplacement = $table2Start . "\n        " . $formStart . "\n            " . $formCsrf . "\n            \n            " . $btnGroupBulk;
$content = str_replace($table2Start, $table2StartReplacement, $content);

// Find the end of Table 2 to add </form>. It ends right before modalHapusSurat
$table2EndPattern = '/(<\/table>\s*<\/div>\s*)(<\/x-card>\s*<x-delete-modal id="modalHapusSurat")/';
$content = preg_replace($table2EndPattern, '$1</form>' . "\n    " . '$2', $content, 1);

// Add hide-bulk class and ID to Table 2 container
$table2Container = '<div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">';
// Ensure we only replace the one in Table 2, which comes after Table 2's header.
// Table 2 header has "Arsip Detail Surat Satuan"
$t2Header = '<p class="text-xs text-gray-400">Pencatatan satuan setiap berkas surat masuk dan keluar</p>
        </div>

        @php
            // Definisi Header (Req 5: Kolom File Dihapus)
            $headers = [\'<input type="checkbox" id="selectAllBulk" onclick="toggleSelectAll()">\', \'No\', \'Tahun\', \'Bulan\', \'Nomor Surat\', \'Tanggal Surat\', \'Judul Surat\', \'Status\', \'Jenis Surat\'];
            if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $headers[] = $k->nama_kolom; } }
            $headers[] = \'Aksi\';
        @endphp

        <div class="overflow-x-auto">';

$t2HeaderReplacement = str_replace('<div class="overflow-x-auto">', '<div id="tableContainerBulk" class="hide-bulk overflow-x-auto">', $t2Header);
$content = str_replace($t2Header, $t2HeaderReplacement, $content);

// Remove "Tabel 2: " text
$content = str_replace('<h3 class="font-bold text-gray-900 text-lg">Tabel 2: Arsip Detail Surat Satuan</h3>', '<h3 class="font-bold text-gray-900 text-lg">Arsip Detail Surat Satuan</h3>', $content);

// 2. Adjust Table 1 Dropdown (make it ID 1, remove Import/Atur Kolom)
$dropdown1Old = <<<HTML
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Impor & Konfigurasi</p>
                    </div>
                    <div class="py-1" role="none">
                        <button type="button" onclick="openModal('modalImportExcel'); toggleDropdown('dropdownOpsiSurat')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Impor Data dari Excel
                        </button>
                        @if(auth()->check() && auth()->user()->isAdmin())
<button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsiSurat')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom Tambahan
                        </button>
@endif
                    </div>
HTML;
$content = str_replace($dropdown1Old, "", $content);
$content = str_replace("dropdownOpsiSurat", "dropdownOpsiSurat1", $content);
// Change text to just "Ekspor Tabel Rekapitulasi" etc.
$content = str_replace("Ekspor Tabel 1 Saja (Rekap)", "Ekspor Tabel Rekapitulasi", $content);
$content = str_replace("Ekspor Tabel 2 Saja (Detail)", "Ekspor Tabel Rincian", $content);
$content = str_replace("Ekspor Tabel 1 & 2 (Semua)", "Ekspor Tabel Lengkap", $content);

// 3. Inject Table 2 Dropdown next to "Catat Surat Satuan"
$tambahDataBtn = <<<HTML
                <button type="button" onclick="openModalTambah()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga hover:bg-orange-600 rounded-xl shadow-sm transition-colors border-none outline-none focus:ring-2 focus:ring-orange-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Catat Surat Satuan
                </button>
HTML;

$dropdown2 = <<<HTML
                <!-- Opsi Lanjutan Tabel 2 -->
                <div class="relative inline-block text-left mr-2">
                    <button type="button" onclick="toggleDropdown('dropdownOpsiSurat2')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium bg-white text-gray-700 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-gray-200">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="dropdownOpsiSurat2" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor Tabel Rincian (Excel/PDF)</p>
                        </div>
                        <div class="py-1" role="none">
                            <a href="{{ route('surat.export.excel', ['jenis' => 'tabel2', 'tahun' => \$tahunFilter, 'bulan' => \$bulanFilter]) }}" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor ke Excel
                            </a>
                            <a href="{{ route('surat.export.pdf', ['jenis' => 'tabel2', 'tahun' => \$tahunFilter, 'bulan' => \$bulanFilter]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor ke PDF
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Impor & Konfigurasi</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportExcel'); toggleDropdown('dropdownOpsiSurat2')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Impor Data dari Excel
                            </button>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsiSurat2')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom Tambahan
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
HTML;
$content = str_replace($tambahDataBtn, $dropdown2 . "\n" . $tambahDataBtn, $content);

// 4. Change "Mode Hapus Massal" button label to "Hapus semua"
$content = str_replace('Mode Hapus Massal', 'Hapus semua', $content);

// Also, the previous script removed the 'Unduh Template Excel' from modalTambah. Let's do that again cleanly.
$modalTambahTemplateOld = <<<HTML
        <!-- Download Template injected -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="{{ route('template.download', 'surat') }}" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>
HTML;
$content = str_replace($modalTambahTemplateOld, "", $content);

file_put_contents($file, $content);
echo "Done.\n";
