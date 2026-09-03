<?php

$file = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$content = file_get_contents($file);

// 1. Add `modalAturKolom1` and `modalAturKolom2`
$target_modals = '    <!-- MODAL IMPORT TABEL 1 -->';
$new_modals = <<<BLADE
    <!-- MODAL ATUR KOLOM TAMBAHAN TABEL 1 -->
    <x-modal id="modalAturKolom1" title="Atur Kolom Tambahan (Tabel 1)" description="Kelola kolom ekstra di luar master dokumen.">
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
            @forelse(\$kolomTabel1 as \$k)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <div>
                        <span class="block text-sm font-semibold text-gray-800">{{ \$k->nama_kolom }}</span>
                        <span class="block text-[10px] text-gray-500 uppercase">{{ \$k->tipe_input }}</span>
                    </div>
                    <form action="{{ route('pa-tekstual.destroyKolom', \$k->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded" onclick="return confirm('Hapus kolom ini?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p>
            @endforelse
        </div>
        <form action="{{ route('pa-tekstual.storeKolom') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 flex flex-col gap-2">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="1">
            <input type="text" name="nama_kolom" required placeholder="Nama Kolom Baru..." class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
            <div class="flex gap-2">
                <select name="tipe_input" required class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500 bg-white">
                    <option value="text">Teks Singkat (Keterangan)</option>
                    <option value="number">Angka (Tanpa Format)</option>
                    <option value="currency">Mata Uang (Rupiah)</option>
                </select>
                <x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL ATUR KOLOM TAMBAHAN TABEL 2 -->
    <x-modal id="modalAturKolom2" title="Atur Kolom Tambahan (Tabel 2)" description="Kelola kolom ekstra di luar master dokumen.">
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
            @forelse(\$kolomTabel2 as \$k)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <div>
                        <span class="block text-sm font-semibold text-gray-800">{{ \$k->nama_kolom }}</span>
                        <span class="block text-[10px] text-gray-500 uppercase">{{ \$k->tipe_input }}</span>
                    </div>
                    <form action="{{ route('pa-tekstual.destroyKolom', \$k->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded" onclick="return confirm('Hapus kolom ini?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p>
            @endforelse
        </div>
        <form action="{{ route('pa-tekstual.storeKolom') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 flex flex-col gap-2">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="2">
            <input type="text" name="nama_kolom" required placeholder="Nama Kolom Baru..." class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
            <div class="flex gap-2">
                <select name="tipe_input" required class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500 bg-white">
                    <option value="text">Teks Singkat (Keterangan)</option>
                    <option value="number">Angka (Tanpa Format)</option>
                    <option value="currency">Mata Uang (Rupiah)</option>
                </select>
                <x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Simpan</x-button>
            </div>
        </form>
    </x-modal>

BLADE;
$content = str_replace($target_modals, $new_modals . "\n" . $target_modals, $content);

// 2. Add container for dynamic columns in `modalEditBulan`
$target_edit = <<<'EOF'
            <input type="hidden" name="bulan" id="edit_bulan">
            <div id="edit_items_container" class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <!-- Inputs rendered via JS -->
            </div>
            <div class="col-span-2 flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100">
EOF;
$replacement_edit = <<<'EOF'
            <input type="hidden" name="bulan" id="edit_bulan">
            <div id="edit_items_container" class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <!-- Inputs rendered via JS -->
            </div>
            <div id="edit_tambahan_container" class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 border-t border-gray-100 pt-4 hidden">
                <!-- Tambahan Inputs rendered via JS -->
            </div>
            <div class="col-span-2 flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100">
EOF;
$content = str_replace($target_edit, $replacement_edit, $content);

// 3. Inject JS schema and update `editBulan` JS function
$target_js = <<<'EOF'
    function editBulan(kelompok, tahun, bulan, itemsJson) {
        document.getElementById('edit_tahun').value = tahun;
        document.getElementById('edit_bulan').value = bulan;
        
        let items = JSON.parse(itemsJson || '{}');
        let masters = kelompok == 1 ? {!! json_encode($masterTabel1) !!} : {!! json_encode($masterTabel2) !!};
        
        let html = '';
        masters.forEach(m => {
            let val = items[m.id] || 0;
            html += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">${m.nama_dokumen}</label>
                    <input type="number" name="items[${m.id}]" value="${val}" required min="0" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:bg-white outline-none">
                </div>
            `;
        });
        
        document.getElementById('edit_items_container').innerHTML = html;
        openModal('modalEditBulan');
    }
EOF;
$replacement_js = <<<'EOF'
    function formatRupiahJs(angka) {
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah ? rupiah : '';
    }

    function sanitizeRupiah(input) {
        let val = input.value.replace(/[^,\d]/g, '');
        input.value = formatRupiahJs(val);
    }

    function editBulan(kelompok, tahun, bulan, itemsJson, tambahanJson = '{}') {
        document.getElementById('edit_tahun').value = tahun;
        document.getElementById('edit_bulan').value = bulan;
        
        let items = JSON.parse(itemsJson || '{}');
        let tambahan = JSON.parse(tambahanJson || '{}');
        let masters = kelompok == 1 ? {!! json_encode($masterTabel1) !!} : {!! json_encode($masterTabel2) !!};
        let koloms = kelompok == 1 ? {!! json_encode($kolomTabel1) !!} : {!! json_encode($kolomTabel2) !!};
        
        let html = '';
        masters.forEach(m => {
            let val = items[m.id] || 0;
            html += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">${m.nama_dokumen}</label>
                    <input type="number" name="items[${m.id}]" value="${val}" required min="0" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:bg-white outline-none">
                </div>
            `;
        });
        document.getElementById('edit_items_container').innerHTML = html;

        // Dynamic Columns
        let htmlTambahan = '';
        koloms.forEach(k => {
            let val = tambahan[k.nama_kolom] || '';
            let inputField = '';
            
            if(k.tipe_input === 'currency') {
                inputField = `<div class="relative"><span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">Rp</span><input type="text" oninput="sanitizeRupiah(this)" name="data_tambahan[${k.nama_kolom}]" value="${formatRupiahJs(val)}" class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:bg-white outline-none"></div>`;
            } else if(k.tipe_input === 'number') {
                inputField = `<input type="number" name="data_tambahan[${k.nama_kolom}]" value="${val}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:bg-white outline-none">`;
            } else {
                inputField = `<input type="text" name="data_tambahan[${k.nama_kolom}]" value="${val}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:bg-white outline-none">`;
            }
            
            htmlTambahan += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">${k.nama_kolom}</label>
                    ${inputField}
                </div>
            `;
        });
        
        let containerTambahan = document.getElementById('edit_tambahan_container');
        if (koloms.length > 0) {
            containerTambahan.innerHTML = htmlTambahan;
            containerTambahan.classList.remove('hidden');
        } else {
            containerTambahan.innerHTML = '';
            containerTambahan.classList.add('hidden');
        }

        openModal('modalEditBulan');
    }
EOF;
$content = str_replace($target_js, $replacement_js, $content);

file_put_contents($file, $content);
echo "View patched step 2.\n";
