<?php

$path = 'resources/views/program-strategis.blade.php';
$content = file_get_contents($path);

// Find the modal block
$startStr = "<!-- MODAL ATUR KOLOM -->";
$endStr = "</x-modal>";
$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr, $startPos);

if ($startPos !== false && $endPos !== false) {
    $endPos += strlen($endStr);
    
    $oldModal = substr($content, $startPos, $endPos - $startPos);
    
    $newModal = <<<HTML
<!-- MODAL ATUR KOLOM -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan" description="Tambah atau hapus kolom khusus pada tabel Program Strategis.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
            @if(isset(\$kolomDinamis) && \$kolomDinamis->count() > 0)
                @foreach(\$kolomDinamis as \$kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ \$kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ \$kolom->tipe_input }}</span> 
                                @if(\$kolom->tipe_input === 'dropdown' && \$kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode(\$kolom->pilihan_dropdown)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="openDeleteModal('modalHapusKolom', '{{ route('program-strategis.kolom.destroy', \$kolom->id) }}')" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach
            @else
                <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan. Tabel menggunakan kolom standar.</p>
            @endif
        </div>
        <form action="{{ route('program-strategis.kolom.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="program_strategis">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">Nama Kolom</label>
                    <input type="text" name="nama_kolom" placeholder="Misal: Catatan Auditor" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputProgram" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500" onchange="toggleDropdownConfig('tipeInputProgram', 'dropdownConfigAreaProgram')">
                        <option value="text">Teks Singkat</option>
                        <option value="number">Angka Kuantitas Biasa</option>
                        <option value="currency">Harga / Uang (Titik Otomatis)</option>
                        <option value="date">Tanggal</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigAreaProgram">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Tinggi, Sedang, Rendah" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button>
                <x-button variant="primary" type="submit" class="!bg-orange-500 hover:!bg-orange-600 border-none text-white">Simpan</x-button>
            </div>
        </form>
    </x-modal>
HTML;

    $content = str_replace($oldModal, $newModal, $content);
    file_put_contents($path, $content);
    echo "modalAturKolom updated successfully!\n";
} else {
    echo "modalAturKolom not found!\n";
}

?>
