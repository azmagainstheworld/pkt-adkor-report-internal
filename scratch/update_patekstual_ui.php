<?php

$file = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$content = file_get_contents($file);

// 1. Add Dropdown 1
$target_btn1 = '<button type="button" id="btnModeBulk1"';
$dropdown1 = <<<BLADE
        <div class="relative inline-block text-left mr-2">
            <button type="button" onclick="toggleDropdown('dropdownOpsi1')" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-white text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-gray-200">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Opsi Lanjutan
            </button>
            <div id="dropdownOpsi1" class="hidden absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden origin-top-right transition-all">
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-100"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Aksi & Laporan</p></div>
                <div class="py-1">
                    <button type="button" onclick="openModal('modalImport1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import dari Excel
                    </button>
                    <a href="{{ route('pa-tekstual.export.excel', ['kelompok_tabel' => 1] + request()->query()) }}" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('pa-tekstual.export.pdf', ['kelompok_tabel' => 1] + request()->query()) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export Laporan PDF
                    </a>
                </div>
                @if(auth()->check() && auth()->user()->isAdmin())
                <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi</p></div>
                <div class="py-1">
                    <button type="button" onclick="openModal('modalMaster1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Atur Kolom
                    </button>
                </div>
                @endif
            </div>
        </div>
BLADE;
$content = str_replace($target_btn1, $dropdown1 . "\n" . '                    ' . $target_btn1, $content);

// 2. Add Dropdown 2
$target_btn2 = '<button type="button" id="btnModeBulk2"';
$dropdown2 = str_replace(['Opsi1', 'Import1', 'Master1', 'kelompok_tabel\' => 1'], ['Opsi2', 'Import2', 'Master2', 'kelompok_tabel\' => 2'], $dropdown1);
$content = str_replace($target_btn2, $dropdown2 . "\n" . '                    ' . $target_btn2, $content);

// 3. Modals logic
$modals = <<<BLADE
    <!-- MODAL ATUR KOLOM TABEL 1 -->
    <x-modal id="modalMaster1" title="Atur Kolom Tabel 1" description="Daftar kolom/dokumen pada Tabel 1.">
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
            @forelse(\$masterTabel1 as \$m)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-sm font-semibold text-gray-800">{{ \$m->nama_dokumen }}</span>
                    <form action="{{ route('pa-tekstual.destroyMaster', \$m->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded" onclick="return confirm('Hapus kolom ini dan semua datanya?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada daftar kegiatan. Tambahkan di bawah.</p>
            @endforelse
        </div>
        <form action="{{ route('pa-tekstual.storeMaster') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="1">
            <input type="text" name="nama_dokumen" required placeholder="Nama Kolom Baru..." class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
            <x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Tambah</x-button>
        </form>
    </x-modal>

    <!-- MODAL ATUR KOLOM TABEL 2 -->
    <x-modal id="modalMaster2" title="Atur Kolom Tabel 2" description="Daftar kolom/dokumen pada Tabel 2.">
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
            @forelse(\$masterTabel2 as \$m)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-sm font-semibold text-gray-800">{{ \$m->nama_dokumen }}</span>
                    <form action="{{ route('pa-tekstual.destroyMaster', \$m->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded" onclick="return confirm('Hapus kolom ini dan semua datanya?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada daftar kegiatan. Tambahkan di bawah.</p>
            @endforelse
        </div>
        <form action="{{ route('pa-tekstual.storeMaster') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="2">
            <input type="text" name="nama_dokumen" required placeholder="Nama Kolom Baru..." class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
            <x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Tambah</x-button>
        </form>
    </x-modal>

    <!-- MODAL IMPORT TABEL 1 -->
    <x-modal id="modalImport1" title="Import Data Tabel 1" description="Upload file Excel untuk menambahkan data massal ke Tabel 1.">
        <form action="{{ route('pa-tekstual.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="1">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors">
                <input type="file" name="file_excel" id="file_excel1" class="hidden" accept=".xlsx, .xls" required onchange="document.getElementById('fileName1').textContent = this.files[0].name">
                <label for="file_excel1" class="cursor-pointer flex flex-col items-center">
                    <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span class="text-sm font-medium text-gray-600">Klik untuk memilih file Excel</span>
                    <span id="fileName1" class="text-xs text-gray-400 mt-1">Format didukung: .xlsx, .xls</span>
                </label>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="{{ route('pa-tekstual.export.excel', ['kelompok_tabel' => 1, 'template' => 1]) }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Template
                </a>
                <div class="flex gap-2">
                    <x-button variant="outline" type="button" onclick="closeModal('modalImport1')" class="!py-1.5 text-xs">Batal</x-button>
                    <x-button variant="primary" type="submit" class="!py-1.5 text-xs bg-emerald-600 hover:bg-emerald-700 border-none">Import Data</x-button>
                </div>
            </div>
        </form>
    </x-modal>

    <!-- MODAL IMPORT TABEL 2 -->
    <x-modal id="modalImport2" title="Import Data Tabel 2" description="Upload file Excel untuk menambahkan data massal ke Tabel 2.">
        <form action="{{ route('pa-tekstual.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="2">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors">
                <input type="file" name="file_excel" id="file_excel2" class="hidden" accept=".xlsx, .xls" required onchange="document.getElementById('fileName2').textContent = this.files[0].name">
                <label for="file_excel2" class="cursor-pointer flex flex-col items-center">
                    <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span class="text-sm font-medium text-gray-600">Klik untuk memilih file Excel</span>
                    <span id="fileName2" class="text-xs text-gray-400 mt-1">Format didukung: .xlsx, .xls</span>
                </label>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="{{ route('pa-tekstual.export.excel', ['kelompok_tabel' => 2, 'template' => 1]) }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Template
                </a>
                <div class="flex gap-2">
                    <x-button variant="outline" type="button" onclick="closeModal('modalImport2')" class="!py-1.5 text-xs">Batal</x-button>
                    <x-button variant="primary" type="submit" class="!py-1.5 text-xs bg-emerald-600 hover:bg-emerald-700 border-none">Import Data</x-button>
                </div>
            </div>
        </form>
    </x-modal>

BLADE;

$content = str_replace('</main>', $modals . "\n</main>", $content);

// Add toggleDropdown script
$script_dropdown = <<<BLADE
    function toggleDropdown(id) {
        let dropdown = document.getElementById(id);
        if (dropdown.classList.contains('hidden')) {
            // Tutup semua dropdown lain dulu
            document.querySelectorAll('[id^="dropdownOpsi"]').forEach(el => el.classList.add('hidden'));
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }
    
    // Close dropdown on click outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdownOpsi"]').forEach(el => el.classList.add('hidden'));
        }
    });

BLADE;
$content = str_replace('function openModal(id)', $script_dropdown . "\n" . '    function openModal(id)', $content);

file_put_contents($file, $content);
echo "Blade UI updated with Opsi Lanjutan and Modals.\n";
