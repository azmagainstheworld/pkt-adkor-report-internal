<?php
$f = 'resources/views/perizinan-perkantoran.blade.php';
$c = file_get_contents($f);

// Teks dropdown Terbit
$dropdownTerbit = <<<'HTML'
            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperTerbit')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiSuperTerbit" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p></div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportPerizinanTerbit'); toggleActionDropdown('dropdownOpsiSuperTerbit')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import Excel
                            </button>
                            <a href="{{ route('perizinan-perkantoran.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Export Excel
                            </a>
                            <a href="{{ route('perizinan-perkantoran.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Export PDF
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p></div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolomTerbit'); toggleActionDropdown('dropdownOpsiSuperTerbit')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom Terbit
                            </button>
                        </div>
                    </div>
                </div>

                <x-button variant="primary" type="button" onclick="openModalTambah()" class="shadow-sm text-xs border-none !py-2 !rounded-xl">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Rincian
                </x-button>
            </div>
HTML;

// Cari bagian Terbit untuk diganti
$oldTerbit = <<<'HTML'
            <div class="flex items-center gap-2">
                <form action="{{ route('perizinan-perkantoran.export-excel') }}" method="GET">
                    <input type="hidden" name="tahun" value="{{ $filterTahun }}">
                    <input type="hidden" name="bulan" value="{{ $filterBulan }}">
                    <x-button variant="success" type="submit" class="shadow-sm text-xs border-none !py-2 !rounded-xl">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </x-button>
                </form>
                
                <x-button variant="primary" type="button" onclick="openModalTambah()" class="shadow-sm text-xs border-none !py-2 !rounded-xl">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Rincian
                </x-button>
            </div>
HTML;

$c = str_replace($oldTerbit, $dropdownTerbit, $c);


// Teks dropdown Proses
$dropdownProses = <<<'HTML'
            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperProses')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiSuperProses" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p></div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportPerizinanProses'); toggleActionDropdown('dropdownOpsiSuperProses')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import Excel
                            </button>
                            <a href="{{ route('perizinan-proses.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Export Excel
                            </a>
                            <a href="{{ route('perizinan-proses.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Export PDF
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p></div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolomProses'); toggleActionDropdown('dropdownOpsiSuperProses')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom Proses
                            </button>
                        </div>
                    </div>
                </div>

                <x-button variant="primary" type="button" onclick="openModalTambahProses()" class="shadow-sm text-xs border-none !py-2 !rounded-xl">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Rincian
                </x-button>
            </div>
HTML;

$oldProses = <<<'HTML'
            <div class="flex items-center gap-2">
                <form action="{{ route('perizinan-proses.export-excel') }}" method="GET">
                    <input type="hidden" name="tahun" value="{{ $filterTahun }}">
                    <x-button variant="success" type="submit" class="shadow-sm text-xs border-none !py-2 !rounded-xl">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </x-button>
                </form>

                <x-button variant="primary" type="button" onclick="openModalTambahProses()" class="shadow-sm text-xs border-none !py-2 !rounded-xl">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Proses
                </x-button>
            </div>
HTML;

$c = str_replace($oldProses, $dropdownProses, $c);

// Toggle script
$js = <<<'HTML'
    function toggleActionDropdown(id) {
        const dd = document.getElementById(id);
        if(dd.classList.contains('hidden')) {
            document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(el => el.classList.add('hidden'));
            dd.classList.remove('hidden');
        } else {
            dd.classList.add('hidden');
        }
    }
    
    document.addEventListener('click', function(e) {
        if(!e.target.closest('.relative.inline-block.text-left')) {
            document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(el => el.classList.add('hidden'));
        }
    });

    document.addEventListener('input', function(e) {
HTML;

$c = str_replace("    document.addEventListener('input', function(e) {", $js, $c);

file_put_contents($f, $c);
echo "Dropdown restored successfully.\n";
