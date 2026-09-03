<?php

$filePath = 'resources/views/undangan.blade.php';
$content = file_get_contents($filePath);

// Change 1: Hapus Massal
$content = str_replace(
    '<button type="button" id="btnModeBulkDetail" onclick="toggleBulkMode(\'detail\')" class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors text-xs font-semibold flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>',
    '<button type="button" id="btnModeBulkDetail" onclick="toggleBulkMode(\'detail\')" class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors text-xs font-semibold flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>',
    $content
);

// Change 2: modalTambahDetail
$tambahDetailOld = <<<HTML
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                    <input name="tahun" type="number" required placeholder="Contoh: 2026" class="w-full px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
                    <select name="bulan" required class="w-full px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors">
                        <option value="">Pilih Bulan...</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as \$b)
                            <option value="{{ \$b }}">{{ \$b }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Undangan <span class="text-red-500">*</span></label>
HTML;

$tambahDetailNew = <<<HTML
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan & Tahun <span class="text-red-500">*</span></label>
                <input type="month" id="periode_input_tambah_detail" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm outline-none focus:border-blue-500 cursor-pointer transition-colors" onchange="syncPeriode(this.value, 'tahun_add_detail', 'bulan_add_detail')">
                <input type="hidden" name="tahun" id="tahun_add_detail">
                <input type="hidden" name="bulan" id="bulan_add_detail">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Undangan <span class="text-red-500">*</span></label>
HTML;

$content = str_replace($tambahDetailOld, $tambahDetailNew, $content);

// Change 3: modalEditDetail
$editDetailOld = <<<HTML
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <input id="edit_tahun" name="tahun" type="number" readonly class="w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-100 cursor-not-allowed text-sm text-gray-500 shadow-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <input id="edit_bulan" name="bulan" type="text" readonly class="w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-100 cursor-not-allowed text-sm text-gray-500 shadow-sm" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Undangan <span class="text-red-500">*</span></label>
HTML;

$editDetailNew = <<<HTML
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan & Tahun</label>
                <input type="month" id="edit_periode_detail" readonly class="w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-100 cursor-not-allowed text-sm text-gray-500 shadow-sm outline-none" />
                <input type="hidden" name="tahun" id="edit_tahun_detail">
                <input type="hidden" name="bulan" id="edit_bulan_detail">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Undangan <span class="text-red-500">*</span></label>
HTML;

$content = str_replace($editDetailOld, $editDetailNew, $content);

// Change 4: editDataDetail JS
$jsOld = <<<HTML
        function editDataDetail(data) {
            document.getElementById('formEditDetail').action = `/administrasi/undangan/detail/\${data.id}`;
            document.getElementById('edit_tahun').value = data.tahun;
            document.getElementById('edit_bulan').value = data.bulan;
            document.getElementById('edit_jenis_undangan').value = data.jenis_undangan;
HTML;

$jsNew = <<<HTML
        function editDataDetail(data) {
            document.getElementById('formEditDetail').action = `/administrasi/undangan/detail/\${data.id}`;
            document.getElementById('edit_tahun_detail').value = data.tahun;
            document.getElementById('edit_bulan_detail').value = data.bulan;
            
            let monthIndex = namaBulanIndo.indexOf(data.bulan) + 1;
            let monthStr = monthIndex < 10 ? '0' + monthIndex : monthIndex;
            document.getElementById('edit_periode_detail').value = data.tahun + '-' + monthStr;

            document.getElementById('edit_jenis_undangan').value = data.jenis_undangan;
HTML;

$content = str_replace($jsOld, $jsNew, $content);

// Change 5: Atur Kolom Tambahan Button Protection
$btnKolomOld = <<<HTML
                        <a href="javascript:void(0)" onclick="openModal('modalAturKolom'); document.getElementById('dropdownOpsiSurat').classList.add('hidden')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                            Atur Kolom Tambahan
                        </a>
HTML;

$btnKolomNew = <<<HTML
@if(auth()->check() && auth()->user()->isAdmin())
                        <a href="javascript:void(0)" onclick="openModal('modalAturKolom'); document.getElementById('dropdownOpsiSurat').classList.add('hidden')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                            Atur Kolom Tambahan
                        </a>
@endif
HTML;

$content = str_replace($btnKolomOld, $btnKolomNew, $content);

file_put_contents($filePath, $content);
echo "Successfully reapplied correct changes to undangan.blade.php\n";

