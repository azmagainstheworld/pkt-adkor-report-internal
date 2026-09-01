<!-- MODALS SECTION -->
<x-import-modal id="modalImportTerbit" route="{{ route('bar-sk-memo.import.terbit') }}" title="Import Data Terbit (CSV)" templateRoute="{{ route('template.download', 'perizinan-terbit') }}" />
<x-import-modal id="modalImportProses" route="{{ route('bar-sk-memo.import.proses') }}" title="Import Data Proses (CSV)" templateRoute="{{ route('template.download', 'perizinan-proses') }}" />
<x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari sistem. Lanjutkan?" />

<!-- ================= MODAL ATUR KOLOM TERBIT ================= -->
<x-modal id="modalAturKolomTerbit" title="Atur Kolom (Tabel Terbit)" description="Tambahkan kolom kustom khusus untuk Tabel Dokumen Terbit.">
    <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
        <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
        @if(isset($kolomDinamisTerbit) && $kolomDinamisTerbit->count() > 0)
            @foreach($kolomDinamisTerbit as $kolom)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                        <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span></p>
                    </div>
                    <button type="button" onclick="triggerDeleteKolom('modalAturKolomTerbit', '{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
            @endforeach
        @else <p class="text-xs text-gray-500 italic">Belum ada kolom.</p> @endif
    </div>
    <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
        @csrf <input type="hidden" name="modul" value="bar_sk_memo_terbit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
            <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                <select name="tipe_input" id="tipeInputTerbit" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('tipeInputTerbit', 'dropdownConfigAreaTerbit')">
                    <option value="text">Teks Singkat</option>
                    <option value="number">Angka Kuantitas Biasa</option>
                    <option value="currency">Harga / Uang (Titik Otomatis)</option>
                    <option value="dropdown">Dropdown (Pilihan)</option>
                </select>
            </div>
            <div class="md:col-span-2 hidden" id="dropdownConfigAreaTerbit">
                <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan koma)</label>
                <input type="text" name="pilihan_dropdown" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolomTerbit')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
    </form>
</x-modal>

<!-- ================= MODAL ATUR KOLOM PROSES ================= -->
<x-modal id="modalAturKolomProses" title="Atur Kolom (Tabel Proses)" description="Tambahkan kolom kustom khusus untuk Tabel Dokumen Proses.">
    <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
        <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
        @if(isset($kolomDinamisProses) && $kolomDinamisProses->count() > 0)
            @foreach($kolomDinamisProses as $kolom)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                        <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span></p>
                    </div>
                    <button type="button" onclick="triggerDeleteKolom('modalAturKolomProses', '{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
            @endforeach
        @else <p class="text-xs text-gray-500 italic">Belum ada kolom.</p> @endif
    </div>
    <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
        @csrf <input type="hidden" name="modul" value="bar_sk_memo_proses">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
            <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                <select name="tipe_input" id="tipeInputProses" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('tipeInputProses', 'dropdownConfigAreaProses')">
                    <option value="text">Teks Singkat</option>
                    <option value="number">Angka Kuantitas Biasa</option>
                    <option value="currency">Harga / Uang (Titik Otomatis)</option>
                    <option value="dropdown">Dropdown (Pilihan)</option>
                </select>
            </div>
            <div class="md:col-span-2 hidden" id="dropdownConfigAreaProses">
                <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan koma)</label>
                <input type="text" name="pilihan_dropdown" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolomProses')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
    </form>
</x-modal>

<!-- ================= MODAL TAMBAH DINAMIS TERBIT ================= -->
<x-modal id="modalTambahTerbit" title="Input Data Terbit" description="Masukkan data dokumen terbit pada bulan tertentu.">
    <form action="{{ route('bar-sk-memo.storeTerbit') }}" method="POST" class="grid grid-cols-2 gap-x-6 gap-y-4 novalidate-form" novalidate>
        @csrf
        <div class="col-span-2 mb-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
            <input type="month" id="picker_t_terbit" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none" onchange="syncPeriode(this.value, 'tt_thn', 'tt_bln')">
            <input type="hidden" name="tahun" id="tt_thn"><input type="hidden" name="bulan" id="tt_bln">
        </div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">SKD Keputusan Bersama</label><input type="number" name="skd_kb" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">SKD Non Ratifikasi</label><input type="number" name="skd_nr" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">SKD Ratifikasi</label><input type="number" name="skd_r" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Memo Direksi</label><input type="number" name="memo" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">BAR Monitoring</label><input type="number" name="bar_mon" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">BAR Manajemen</label><input type="number" name="bar_man" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        
        @if(isset($kolomDinamisTerbit))
            @foreach($kolomDinamisTerbit as $kolom)
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                    @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-terbit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none">
                    @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-terbit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none">
                    @elseif($kolom->tipe_input === 'currency')<div class="relative"><div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500 text-xs">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-terbit w-full pl-7 pr-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none"></div>
                    @elseif($kolom->tipe_input === 'dropdown')
                        <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-terbit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none">
                            <option value="">Pilih...</option>
                            @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pil)<option value="{{ trim($pil) }}">{{ trim($pil) }}</option>@endforeach @endif
                        </select>
                    @endif
                </div>
            @endforeach
        @endif

        <div class="col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4"><x-button variant="outline" type="button" onclick="closeModal('modalTambahTerbit')">Batal</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
    </form>
</x-modal>

<!-- ================= MODAL TAMBAH DINAMIS PROSES ================= -->
<x-modal id="modalTambahProses" title="Input Data Proses" description="Masukkan data dokumen proses pada bulan tertentu.">
    <form action="{{ route('bar-sk-memo.storeProses') }}" method="POST" class="grid grid-cols-2 gap-x-6 gap-y-4 novalidate-form" novalidate>
        @csrf
        <div class="col-span-2 mb-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
            <input type="month" id="picker_t_proses" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none" onchange="syncPeriode(this.value, 'tp_thn', 'tp_bln')">
            <input type="hidden" name="tahun" id="tp_thn"><input type="hidden" name="bulan" id="tp_bln">
        </div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Proses SKD Keputusan Bersama</label><input type="number" name="skd_kb" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Proses SKD Non Ratifikasi</label><input type="number" name="skd_nr" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Proses SKD Ratifikasi</label><input type="number" name="skd_r" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Proses Memo Direksi</label><input type="number" name="memo" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Proses BAR Monitoring</label><input type="number" name="bar_mon" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Proses BAR Manajemen</label><input type="number" name="bar_man" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:border-blue-500 outline-none"></div>
        
        @if(isset($kolomDinamisProses))
            @foreach($kolomDinamisProses as $kolom)
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                    @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan_proses[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-proses w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none">
                    @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan_proses[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-proses w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none">
                    @elseif($kolom->tipe_input === 'currency')<div class="relative"><div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500 text-xs">Rp</div><input type="text" name="data_tambahan_proses[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-proses w-full pl-7 pr-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none"></div>
                    @elseif($kolom->tipe_input === 'dropdown')
                        <select name="data_tambahan_proses[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-proses w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none">
                            <option value="">Pilih...</option>
                            @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pil)<option value="{{ trim($pil) }}">{{ trim($pil) }}</option>@endforeach @endif
                        </select>
                    @endif
                </div>
            @endforeach
        @endif

        <div class="col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4"><x-button variant="outline" type="button" onclick="closeModal('modalTambahProses')">Batal</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
    </form>
</x-modal>
