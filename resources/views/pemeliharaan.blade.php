@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm flex flex-col shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-bold">Gagal menyimpan data:</span>
            </div>
            <ul class="list-disc list-inside pl-8 text-xs">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Pemeliharaan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pemeliharaan Peralatan & Furnitur</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('pemeliharaan.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-orange-500">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-orange-500">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART DINAMIS ================= -->
    <x-card class="!rounded-xl overflow-hidden shadow-sm border border-gray-100 mb-8 p-6 bg-white">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Statistik Pemeliharaan & Investasi Rutin</h3>
                <p class="text-xs text-gray-400">Distribusi jumlah kegiatan pemeliharaan (Tabel Pertama)</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                @foreach($masterRutin as $index => $master)
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-md" style="background-color: {{ $chartColors[$index % count($chartColors)] }}"></span>
                        <span class="text-gray-700 font-medium">{{ $master->nama_kegiatan }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="h-64 w-full relative">
            <canvas id="pemeliharaanChart"></canvas>
        </div>
    </x-card>

    <!-- ================= TABEL 1: PEMELIHARAAN RUTIN DINAMIS ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Pemeliharaan & Penyediaan Peralatan</h3>
                <p class="text-xs text-gray-400">Akumulasi bulanan pemeliharaan dan penyiapan baru</p>
            </div>
            <x-button variant="primary" onclick="openModalTambahRutin()" class="!py-2 text-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pemeliharaan Rutin
            </x-button>
        </div>

        <div class="overflow-x-auto">
            @php
                $headersRutin = ['Tahun', 'Bulan'];
                foreach($masterRutin as $master) { $headersRutin[] = $master->nama_kegiatan; }
                $headersRutin[] = 'Aksi';
            @endphp
            <x-table :headers="$headersRutin">
                @forelse($dataRutinTable as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        
                        @foreach($masterRutin as $master)
                            @php $jumlah = $row['items'][$master->id] ?? 0; @endphp
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $jumlah }}</td>
                        @endforeach

                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                @php $itemsJson = json_encode($row['items']); @endphp
                                <button type="button" onclick="editRutin('{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusRutin', '{{ route('pemeliharaan-rutin.destroyBulan', ['tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headersRutin) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data pemeliharaan rutin.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PERALATAN DINAMIS ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Rincian Perbaikan Inventaris</h3>
                <p class="text-xs text-gray-400">Data spesifik perbaikan fasilitas kantor</p>
            </div>
            <x-button variant="primary" onclick="openModalTambahPeralatan()" class="!py-2 text-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Rincian Perbaikan
            </x-button>
        </div>

        <div class="overflow-x-auto">
            @php
                $headersAlat = ['Tahun', 'Bulan'];
                foreach($masterPeralatan as $master) { $headersAlat[] = $master->nama_peralatan; }
                $headersAlat[] = 'Aksi';
            @endphp
            <x-table :headers="$headersAlat">
                @forelse($dataPeralatanTable as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        
                        @foreach($masterPeralatan as $master)
                            @php $jumlah = $row['items'][$master->id] ?? 0; @endphp
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $jumlah }}</td>
                        @endforeach

                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                @php $itemsJson = json_encode($row['items']); @endphp
                                <button type="button" onclick="editPeralatan('{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusPeralatan', '{{ route('pemeliharaan-peralatan.destroyBulan', ['tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headersAlat) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data rincian perbaikan.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- Panggil Komponen Modal Hapus -->
    <x-delete-modal id="modalHapusRutin" title="Hapus Pemeliharaan Rutin" message="Data pemeliharaan rutin pada bulan ini akan dihapus secara permanen." />
    <x-delete-modal id="modalHapusPeralatan" title="Hapus Rincian Perbaikan" message="Seluruh rincian perbaikan pada bulan ini akan dihapus secara permanen." />

    <!-- ================= MODAL TAMBAH TABEL 1 (RUTIN) ================= -->
    <x-modal id="modalTambahRutin" title="Tambah Pemeliharaan Rutin" description="Pilih kegiatan dan masukkan jumlah bulannya. Anda juga bisa menambah jenis kegiatan baru.">
        <form action="{{ route('pemeliharaan-rutin.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <!-- Kalendar Khusus Bulan/Tahun -->
                <input type="month" id="picker_tambah_rutin" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'rutin_tahun', 'rutin_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <!-- Hidden Input untuk Backend -->
                <input type="hidden" name="tahun" id="rutin_tahun">
                <input type="hidden" name="bulan" id="rutin_bulan">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kegiatan <span class="text-red-500">*</span></label>
                <select name="rutin_id" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="toggleRutinBaru(this.value)">
                    <option value="" disabled selected>Pilih Jenis...</option>
                    @foreach($masterRutin as $master)
                        <option value="{{ $master->id }}">{{ $master->nama_kegiatan }}</option>
                    @endforeach
                    <option value="tambah_baru" class="font-bold text-orange-600">++ Tambah Kegiatan Baru ++</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bidang ini wajib dipilih!</span>
            </div>
            
            <div class="md:col-span-2 hidden" id="wrap_rutin_baru">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kegiatan Baru (Cth: Pengadaan Tinta) <span class="text-red-500">*</span></label>
                <input type="text" name="nama_rutin_baru" id="input_rutin_baru" placeholder="Ketik nama kegiatan..." class="w-full px-4 py-2 bg-orange-50 border border-orange-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Nama kegiatan baru wajib diisi!</span>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" required placeholder="Contoh: 5" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Jumlah wajib diisi!</span>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahRutin')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL EDIT TABEL 1 (RUTIN MASSAL) ================= -->
    <x-modal id="modalEditRutin" title="Edit Pemeliharaan Rutin" description="Perbarui seluruh data pemeliharaan rutin beserta periodenya.">
        <form action="{{ route('pemeliharaan-rutin.updateBulan') }}" method="POST" class="grid grid-cols-2 gap-x-4 gap-y-4 novalidate-form" novalidate>
            @csrf
            <!-- Hidden Input untuk mendeteksi data lama -->
            <input type="hidden" name="old_tahun" id="old_rutin_tahun">
            <input type="hidden" name="old_bulan" id="old_rutin_bulan">

            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_edit_rutin" required class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500" onchange="syncPeriode(this.value, 'edit_rutin_tahun', 'edit_rutin_bulan')">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                <input type="hidden" name="tahun" id="edit_rutin_tahun">
                <input type="hidden" name="bulan" id="edit_rutin_bulan">
            </div>

            <div class="col-span-2 border-b border-gray-100 my-1"></div>

            @foreach($masterRutin as $master)
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 truncate" title="{{ $master->nama_kegiatan }}">{{ $master->nama_kegiatan }}</label>
                    <input type="number" name="items[{{ $master->id }}]" id="edit_rutin_item_{{ $master->id }}" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                </div>
            @endforeach

            <div class="col-span-2 flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEditRutin')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH TABEL 2 (PERALATAN) ================= -->
    <x-modal id="modalTambahPeralatan" title="Tambah Rincian Pemeliharaan Alat" description="Pilih jenis peralatan dan masukkan jumlahnya.">
        <form action="{{ route('pemeliharaan-peralatan.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_tambah_peralatan" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'alat_tahun', 'alat_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="alat_tahun">
                <input type="hidden" name="bulan" id="alat_bulan">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Pemeliharaan <span class="text-red-500">*</span></label>
                <select name="peralatan_id" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="togglePeralatanBaru(this.value)">
                    <option value="" disabled selected>Pilih Jenis...</option>
                    @foreach($masterPeralatan as $master)
                        <option value="{{ $master->id }}">{{ $master->nama_peralatan }}</option>
                    @endforeach
                    <option value="tambah_baru" class="font-bold text-orange-600">++ Tambah Variabel Baru ++</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bidang ini wajib dipilih!</span>
            </div>
            
            <div class="md:col-span-2 hidden" id="wrap_peralatan_baru">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Variabel Baru (Cth: Atap Bocor) <span class="text-red-500">*</span></label>
                <input type="text" name="nama_peralatan_baru" id="input_peralatan_baru" placeholder="Ketik nama perbaikan..." class="w-full px-4 py-2 bg-orange-50 border border-orange-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Nama variabel baru wajib diisi!</span>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" required placeholder="Contoh: 5" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Jumlah wajib diisi!</span>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahPeralatan')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL EDIT TABEL 2 (PERALATAN MASSAL) ================= -->
    <x-modal id="modalEditPeralatan" title="Edit Perbaikan Alat" description="Perbarui seluruh data perbaikan beserta periodenya.">
        <form action="{{ route('pemeliharaan-peralatan.updateBulan') }}" method="POST" id="formEditPeralatan" class="grid grid-cols-2 gap-x-4 gap-y-4 novalidate-form" novalidate>
            @csrf
            <!-- Hidden Input untuk merekam data lama -->
            <input type="hidden" name="old_tahun" id="old_alat_tahun">
            <input type="hidden" name="old_bulan" id="old_alat_bulan">

            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_edit_alat" required class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500" onchange="syncPeriode(this.value, 'edit_alat_tahun', 'edit_alat_bulan')">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                <input type="hidden" name="tahun" id="edit_alat_tahun">
                <input type="hidden" name="bulan" id="edit_alat_bulan">
            </div>

            <div class="col-span-2 border-b border-gray-100 my-1"></div>

            @foreach($masterPeralatan as $master)
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 truncate" title="{{ $master->nama_peralatan }}">{{ $master->nama_peralatan }}</label>
                    <input type="number" name="items[{{ $master->id }}]" id="edit_alat_item_{{ $master->id }}" value="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                </div>
            @endforeach

            <div class="col-span-2 flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEditPeralatan')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // ==========================================================
    // Skrip Parsing Kalendar (Month Picker)
    // ==========================================================
    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
            
            // Hapus style error saat diisi
            if(event && event.target) {
                const picker = event.target;
                picker.classList.remove('border-red-500', 'bg-red-50');
                const err = picker.nextElementSibling;
                if(err && err.classList.contains('error-msg')) err.classList.add('hidden');
            }
        } else {
            document.getElementById(yearId).value = '';
            document.getElementById(monthId).value = '';
        }
    }

    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) {
            const monthStr = String(monthIndex + 1).padStart(2, '0');
            return `${tahun}-${monthStr}`;
        }
        return '';
    }

    // Toggle Variabel Baru (Munculkan Input Text & Set Required)
    function toggleRutinBaru(value) {
        const wrap = document.getElementById('wrap_rutin_baru');
        const input = document.getElementById('input_rutin_baru');
        const errorSpan = input.nextElementSibling;
        if (value === 'tambah_baru') { 
            wrap.classList.remove('hidden'); 
            input.setAttribute('required', 'required'); 
        } else { 
            wrap.classList.add('hidden'); 
            input.removeAttribute('required'); 
            input.classList.remove('border-red-500', 'bg-red-50');
            if (errorSpan) errorSpan.classList.add('hidden');
        }
    }

    function togglePeralatanBaru(value) {
        const wrap = document.getElementById('wrap_peralatan_baru');
        const input = document.getElementById('input_peralatan_baru');
        const errorSpan = input.nextElementSibling;
        if (value === 'tambah_baru') { 
            wrap.classList.remove('hidden'); 
            input.setAttribute('required', 'required'); 
        } else { 
            wrap.classList.add('hidden'); 
            input.removeAttribute('required'); 
            input.classList.remove('border-red-500', 'bg-red-50');
            if (errorSpan) errorSpan.classList.add('hidden');
        }
    }

    // ==========================================================
    // Modal Action Triggers
    // ==========================================================
    function openModalTambahRutin() { 
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah_rutin').value = currentMonth;
        syncPeriode(currentMonth, 'rutin_tahun', 'rutin_bulan');
        openModal('modalTambahRutin'); 
    }
    
    function openModalTambahPeralatan() { 
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah_peralatan').value = currentMonth;
        syncPeriode(currentMonth, 'alat_tahun', 'alat_bulan');
        openModal('modalTambahPeralatan'); 
    }

    function editRutin(tahun, bulan, itemsJson) {
        // Tanamkan inputan data lama ke dalam *hidden input*
        document.getElementById('old_rutin_tahun').value = tahun;
        document.getElementById('old_rutin_bulan').value = bulan;

        const pickerVal = getMonthPickerValue(tahun, bulan);
        document.getElementById('picker_edit_rutin').value = pickerVal;
        syncPeriode(pickerVal, 'edit_rutin_tahun', 'edit_rutin_bulan');
        
        const items = JSON.parse(itemsJson);
        @foreach($masterRutin as $master)
            document.getElementById('edit_rutin_item_{{ $master->id }}').value = items[{{ $master->id }}] || 0;
        @endforeach
        openModal('modalEditRutin');
    }

    function editPeralatan(tahun, bulan, itemsJson) {
        // Tanamkan inputan data lama ke dalam *hidden input*
        document.getElementById('old_alat_tahun').value = tahun;
        document.getElementById('old_alat_bulan').value = bulan;

        const pickerVal = getMonthPickerValue(tahun, bulan);
        document.getElementById('picker_edit_alat').value = pickerVal;
        syncPeriode(pickerVal, 'edit_alat_tahun', 'edit_alat_bulan');
        
        const items = JSON.parse(itemsJson);
        @foreach($masterPeralatan as $master)
            document.getElementById('edit_alat_item_{{ $master->id }}').value = items[{{ $master->id }}] || 0;
        @endforeach
        openModal('modalEditPeralatan');
    }

    // Client-Side Validation Logic (Teks Merah)
    const forms = document.querySelectorAll('.novalidate-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500', 'bg-red-50'); 
                    field.classList.remove('border-gray-300', 'border-orange-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden'); 
                } else {
                    field.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden'); 
                }
            });

            if (!isValid) e.preventDefault(); 
        });

        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
    });

    // Chart.js Dinamis Setup
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('pemeliharaanChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const rawChartData = {!! json_encode($chartData) !!};
            const colors = {!! json_encode($chartColors) !!};
            const datasets = [];

            @foreach($masterRutin as $index => $master)
                datasets.push({
                    label: '{!! addslashes($master->nama_kegiatan) !!}',
                    data: rawChartData.map(d => d.items[{{ $master->id }}] || 0),
                    backgroundColor: colors[{{ $index }} % colors.length],
                    borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8
                });
            @endforeach

            new Chart(ctx, {
                type: 'bar',
                data: { labels: rawChartData.map(d => d.label), datasets: datasets },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#F3F4F6', drawBorder: false } },
                        x: { grid: { display: false, drawBorder: false } }
                    }
                }
            });
        }
    });
</script>
@endsection