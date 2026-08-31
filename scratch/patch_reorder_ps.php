<?php
$file = 'resources/views/program-strategis.blade.php';
$content = file_get_contents($file);

$search = '<div class="flex gap-2">
                <x-button type="button" onclick="openModalTambahMaster()" class="!py-1.5 !px-3 text-xs bg-orange-500 hover:bg-orange-600 text-white border-none font-medium flex items-center gap-1 shadow-sm rounded-xl">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Sasaran & Program Induk
                </x-button>

                <!-- DROPDOWN OPSI LANJUTAN -->
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleDropdown(\'dropdownOpsiSuper\')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-orange-200 shadow-sm px-4 py-1.5 bg-white text-xs font-medium text-orange-700 hover:bg-orange-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div id="dropdownOpsiSuper" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="py-1" role="menu">
                            
                            <button type="button" onclick="openModal(\'modalAturKolom\')" class="w-full text-left px-4 py-2.5 text-xs text-gray-700 hover:bg-orange-50 hover:text-orange-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                Atur Kolom Tambahan
                            </button>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <a href="{{ route(\'program-strategis.export.excel\', [\'tahun\' => $filterTahun]) }}" class="w-full text-left px-4 py-2.5 text-xs text-green-700 hover:bg-green-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Export ke Excel
                            </a>
                            <a href="{{ route(\'program-strategis.export.pdf\', [\'tahun\' => $filterTahun]) }}" target="_blank" class="w-full text-left px-4 py-2.5 text-xs text-red-700 hover:bg-red-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Cetak PDF
                            </a>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <button type="button" onclick="openModal(\'modalImport\')" class="w-full text-left px-4 py-2.5 text-xs text-blue-700 hover:bg-blue-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import dari Excel
                            </button>
                        </div>
                    </div>
                </div>

                <x-button variant="primary" onclick="openModalTambah()" class="!py-1.5 !px-3 text-xs">+ Rincian Kegiatan</x-button>';

$replace = '<div class="flex gap-2">
                <!-- DROPDOWN OPSI LANJUTAN -->
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleDropdown(\'dropdownOpsiSuper\')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-orange-200 shadow-sm px-4 py-1.5 bg-white text-xs font-medium text-orange-700 hover:bg-orange-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div id="dropdownOpsiSuper" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="py-1" role="menu">
                            
                            <button type="button" onclick="openModal(\'modalAturKolom\')" class="w-full text-left px-4 py-2.5 text-xs text-gray-700 hover:bg-orange-50 hover:text-orange-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                Atur Kolom Tambahan
                            </button>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <a href="{{ route(\'program-strategis.export.excel\', [\'tahun\' => $filterTahun]) }}" class="w-full text-left px-4 py-2.5 text-xs text-green-700 hover:bg-green-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Export ke Excel
                            </a>
                            <a href="{{ route(\'program-strategis.export.pdf\', [\'tahun\' => $filterTahun]) }}" target="_blank" class="w-full text-left px-4 py-2.5 text-xs text-red-700 hover:bg-red-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Cetak PDF
                            </a>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <button type="button" onclick="openModal(\'modalImport\')" class="w-full text-left px-4 py-2.5 text-xs text-blue-700 hover:bg-blue-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import dari Excel
                            </button>
                        </div>
                    </div>
                </div>

                <x-button type="button" onclick="openModalTambahMaster()" class="!py-1.5 !px-3 text-xs bg-orange-500 hover:bg-orange-600 text-white border-none font-medium flex items-center gap-1 shadow-sm rounded-xl">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Sasaran & Program Strategis
                </x-button>

                <x-button variant="primary" onclick="openModalTambah()" class="!py-1.5 !px-3 text-xs flex items-center gap-1 shadow-sm rounded-xl border-none font-medium text-white">+ Tambah Rincian Data</x-button>';

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Reordered and renamed buttons.\n";
