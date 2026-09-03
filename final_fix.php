<?php
$file = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($file);

// 1. Fix the SyntaxError by removing the stray <script> tag.
$content = str_replace("        }\n\n<script>\n    function openModal", "        }\n\n    function openModal", $content);

// Just in case it's slightly different formatting:
$content = preg_replace('/}\s*<script>\s*function openModal\(/', "}\n\n    function openModal(", $content);

// 2. Move Opsi Lanjutan to Table 1 Header.
// First, extract Table 1 Dropdown from where we put it (Wait, in rewrite_layout.php I actually deleted the Table 1 Dropdown from the Action Bar, but I didn't inject it into Table 1! Let me check).
// Wait! If I deleted it from Action Bar and didn't put it in Table 1, it's completely gone! Let me recreate it.
$tabel1Header = <<<HTML
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Akumulasi Laporan Surat Masuk dan Kekuar</h3>
            <p class="text-xs text-gray-400">Total surat berstatus <span class="text-green-600 font-medium">"Terkirim"</span> terfilter Tahun: {{ \$tahunFilter }}, Bulan: {{ \$bulanFilter }}</p>
        </div>
HTML;

$tabel1HeaderNew = <<<HTML
        <div class="p-5 border-b border-gray-100 bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Akumulasi Laporan Surat Masuk dan Kekuar</h3>
                    <p class="text-xs text-gray-400">Total surat berstatus <span class="text-green-600 font-medium">"Terkirim"</span> terfilter Tahun: {{ \$tahunFilter }}, Bulan: {{ \$bulanFilter }}</p>
                </div>
                <!-- Opsi Lanjutan Table 1 -->
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleDropdown('dropdownOpsiSurat1')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="dropdownOpsiSurat1" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor Tabel Rekapitulasi (Excel/PDF)</p>
                        </div>
                        <div class="py-1" role="none">
                            <a href="{{ route('surat.export.excel', ['jenis' => 'tabel1', 'tahun' => \$tahunFilter, 'bulan' => \$bulanFilter]) }}" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor ke Excel
                            </a>
                            <a href="{{ route('surat.export.pdf', ['jenis' => 'tabel1', 'tahun' => \$tahunFilter, 'bulan' => \$bulanFilter]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor ke PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
HTML;
$content = str_replace($tabel1Header, $tabel1HeaderNew, $content);

// 3. Move Table 2 Action Bar into Table 2 Header.
// Current Table 2 Header:
$tabel2HeaderOld = <<<HTML
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Arsip Detail Surat Satuan</h3>
            <p class="text-xs text-gray-400">Pencatatan satuan setiap berkas surat masuk dan keluar</p>
        </div>
HTML;

// Find the Action Bar that is currently above Table 2
$actionBarPattern = '/<!-- ================= Req 4: ACTION BAR DIPINDAH KE ATAS TABEL 2 ================= -->\s*<div class="flex justify-end items-center mb-5">\s*<div class="flex flex-row items-center gap-3">([\s\S]*?)<\/div>\s*<\/div>\s*<\/div>/';
preg_match($actionBarPattern, $content, $matches);

if (!empty($matches)) {
    $actionBarContent = $matches[1];
    
    // Remove Action Bar from outside
    $content = preg_replace($actionBarPattern, '', $content, 1);
    
    // Create new Table 2 Header
    $tabel2HeaderNew = <<<HTML
        <div class="p-5 border-b border-gray-100 bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Arsip Detail Surat Satuan</h3>
                    <p class="text-xs text-gray-400">Pencatatan satuan setiap berkas surat masuk dan keluar</p>
                </div>
                <div class="flex flex-row items-center gap-3">
                    $actionBarContent
                </div>
            </div>
        </div>
HTML;
    $content = str_replace($tabel2HeaderOld, $tabel2HeaderNew, $content);
}

file_put_contents($file, $content);
echo "Layout fixes and JS syntax error repaired.\n";
