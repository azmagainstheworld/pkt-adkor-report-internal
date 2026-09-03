<?php

$file = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$content = file_get_contents($file);

// 1. Fix colspan="2" to colspan="3"
$content = str_replace('<td colspan="2" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>', '<td colspan="3" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>', $content);

// 2. Remove "++ Tambah Variabel Baru ++" from JS `openModalTambah`
$target_js_tambah = <<<'EOF'
        masters.forEach(m => { select.innerHTML += `<option value="${m.id}">${m.nama_dokumen}</option>`; });
        select.innerHTML += '<option value="tambah_baru" class="font-bold text-orange-600">++ Tambah Variabel Baru ++</option>';
        
        const now = new Date();
EOF;
$replacement_js_tambah = <<<'EOF'
        masters.forEach(m => { select.innerHTML += `<option value="${m.id}">${m.nama_dokumen}</option>`; });
        
        const now = new Date();
EOF;
$content = str_replace($target_js_tambah, $replacement_js_tambah, $content);

// 3. Remove `wrap_var_baru` and toggleVarBaru JS
$target_wrap = <<<'EOF'
            <div class="md:col-span-2 hidden" id="wrap_var_baru">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Variabel Baru <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kegiatan_baru" id="input_var_baru" placeholder="Ketik nama kolom/kegiatan..." class="w-full px-4 py-2 bg-orange-50 border border-orange-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
EOF;
$content = str_replace($target_wrap, '', $content);

$content = preg_replace('/onchange="toggleVarBaru\(this\.value\)"/', '', $content);
$content = preg_replace('/<option value="tambah_baru".*?<\/option>/', '', $content);

$target_toggleJS = <<<'EOF'
    function toggleVarBaru(value) {
        const wrap = document.getElementById('wrap_var_baru');
        const input = document.getElementById('input_var_baru');
        if (value === 'tambah_baru') { 
            wrap.classList.remove('hidden'); input.setAttribute('required', 'required'); 
        } else { 
            wrap.classList.add('hidden'); input.removeAttribute('required'); 
            input.classList.remove('border-red-500', 'bg-red-50');
            if (input.nextElementSibling) input.nextElementSibling.classList.add('hidden');
        }
    }
EOF;
$content = str_replace($target_toggleJS, '', $content);

// 4. Render Kolom in Headers and Rows
$target_head1 = <<<'EOF'
            @php
                $headTabel1 = ['<input type="checkbox" id="selectAllBulk1" class="bulk-cb-header-1 hidden" onclick="toggleSelectAll1()">', 'Tahun', 'Bulan'];
                foreach($masterTabel1 as $master) { $headTabel1[] = $master->nama_dokumen; }
                $headTabel1[] = 'Aksi';
            @endphp
EOF;
$replacement_head1 = <<<'EOF'
            @php
                $headTabel1 = ['<input type="checkbox" id="selectAllBulk1" class="bulk-cb-header-1 hidden" onclick="toggleSelectAll1()">', 'Tahun', 'Bulan'];
                foreach($masterTabel1 as $master) { $headTabel1[] = $master->nama_dokumen; }
                foreach($kolomTabel1 as $kolom) { $headTabel1[] = $kolom->nama_kolom; }
                $headTabel1[] = 'Aksi';
            @endphp
EOF;
$content = str_replace($target_head1, $replacement_head1, $content);

$target_head2 = <<<'EOF'
            @php
                $headTabel2 = ['<input type="checkbox" id="selectAllBulk2" class="bulk-cb-header-2 hidden" onclick="toggleSelectAll2()">', 'Tahun', 'Bulan'];
                foreach($masterTabel2 as $master) { $headTabel2[] = $master->nama_dokumen; }
                $headTabel2[] = 'Aksi';
            @endphp
EOF;
$replacement_head2 = <<<'EOF'
            @php
                $headTabel2 = ['<input type="checkbox" id="selectAllBulk2" class="bulk-cb-header-2 hidden" onclick="toggleSelectAll2()">', 'Tahun', 'Bulan'];
                foreach($masterTabel2 as $master) { $headTabel2[] = $master->nama_dokumen; }
                foreach($kolomTabel2 as $kolom) { $headTabel2[] = $kolom->nama_kolom; }
                $headTabel2[] = 'Aksi';
            @endphp
EOF;
$content = str_replace($target_head2, $replacement_head2, $content);

$target_row1 = <<<'EOF'
                        @foreach($masterTabel1 as $master)
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach
                        <td class="px-4 py-3 text-center">
EOF;
$replacement_row1 = <<<'EOF'
                        @foreach($masterTabel1 as $master)
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach
                        @foreach($kolomTabel1 as $kolom)
                            <td class="px-4 py-3 text-gray-600 text-center">
                                @if($kolom->tipe_input === 'currency' && isset($row['data_tambahan'][$kolom->nama_kolom]))
                                    Rp {{ number_format((float)$row['data_tambahan'][$kolom->nama_kolom], 0, ',', '.') }}
                                @else
                                    {{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="px-4 py-3 text-center">
EOF;
$content = str_replace($target_row1, $replacement_row1, $content);

$target_row2 = <<<'EOF'
                        @foreach($masterTabel2 as $master)
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach
                        <td class="px-4 py-3 text-center">
EOF;
$replacement_row2 = <<<'EOF'
                        @foreach($masterTabel2 as $master)
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach
                        @foreach($kolomTabel2 as $kolom)
                            <td class="px-4 py-3 text-gray-600 text-center">
                                @if($kolom->tipe_input === 'currency' && isset($row['data_tambahan'][$kolom->nama_kolom]))
                                    Rp {{ number_format((float)$row['data_tambahan'][$kolom->nama_kolom], 0, ',', '.') }}
                                @else
                                    {{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="px-4 py-3 text-center">
EOF;
$content = str_replace($target_row2, $replacement_row2, $content);

// Fix editBulan button JSON (pass data_tambahan)
$content = str_replace(
    "@php \$itemsJson = json_encode(\$row['items']); @endphp\n                                <button type=\"button\" onclick=\"editBulan(1, '{{ \$row['tahun'] }}', '{{ \$row['bulan'] }}', '{{ \$itemsJson }}')\"", 
    "@php \$itemsJson = json_encode(\$row['items']); \$tambahanJson = json_encode(\$row['data_tambahan'] ?? []); @endphp\n                                <button type=\"button\" onclick=\"editBulan(1, '{{ \$row['tahun'] }}', '{{ \$row['bulan'] }}', '{{ \$itemsJson }}', '{{ \$tambahanJson }}')\"", 
    $content
);
$content = str_replace(
    "@php \$itemsJson = json_encode(\$row['items']); @endphp\n                                <button type=\"button\" onclick=\"editBulan(2, '{{ \$row['tahun'] }}', '{{ \$row['bulan'] }}', '{{ \$itemsJson }}')\"", 
    "@php \$itemsJson = json_encode(\$row['items']); \$tambahanJson = json_encode(\$row['data_tambahan'] ?? []); @endphp\n                                <button type=\"button\" onclick=\"editBulan(2, '{{ \$row['tahun'] }}', '{{ \$row['bulan'] }}', '{{ \$itemsJson }}', '{{ \$tambahanJson }}')\"", 
    $content
);

// Add Atur Kolom dropdown items
$target_dropdown1 = <<<'EOF'
                    <button type="button" onclick="openModal('modalMaster1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Atur Kolom
                    </button>
EOF;
$replacement_dropdown1 = <<<'EOF'
                    <button type="button" onclick="openModal('modalMaster1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Atur Dokumen
                    </button>
                    <button type="button" onclick="openModal('modalAturKolom1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium border-t border-gray-50">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Tambahan
                    </button>
EOF;
$content = str_replace($target_dropdown1, $replacement_dropdown1, $content);

$target_dropdown2 = <<<'EOF'
                    <button type="button" onclick="openModal('modalMaster2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Atur Kolom
                    </button>
EOF;
$replacement_dropdown2 = <<<'EOF'
                    <button type="button" onclick="openModal('modalMaster2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Atur Dokumen
                    </button>
                    <button type="button" onclick="openModal('modalAturKolom2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium border-t border-gray-50">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Tambahan
                    </button>
EOF;
$content = str_replace($target_dropdown2, $replacement_dropdown2, $content);

file_put_contents($file, $content);
echo "View patched step 1.\n";
