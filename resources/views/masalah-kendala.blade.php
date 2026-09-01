@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    <!-- ================= MODAL ERROR KUSTOM ================= -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Penambahan Gagal</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none'; openModal('modalAturKolom');" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Kembali & Perbaiki
            </button>
        </div>
    </div>
    @endif

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
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Masalah & Kendala</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Masalah / Kendala Operasional</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('masalah-kendala.index') }}" method="GET" class="flex items-center gap-3">
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

    <!-- ================= TABEL DATA ================= -->
    <x-card class="!rounded-xl !p-0 shadow-sm border border-gray-100 bg-white mb-8 relative">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 rounded-t-xl">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Masalah dan Kendala</h3>
                <p class="text-xs text-gray-400">Pencatatan kendala operasional beserta solusi yang telah dilakukan</p>
            </div>
            
            <!-- ACTION BAR MINIMALIS -->
            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperMasalah')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiSuperMasalah" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportMasalah'); toggleActionDropdown('dropdownOpsiSuperMasalah')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Excel
                            </button>
                            <a href="{{ route('masalah-kendala.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export Excel
                            </a>
                            <a href="{{ route('masalah-kendala.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Export PDF
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolom'); toggleActionDropdown('dropdownOpsiSuperMasalah')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
                    </div>
                </div>

                <x-button variant="primary" onclick="openModalTambah()" class="!py-2 text-xs border-none !rounded-xl shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Data
                </x-button>
            </div>
        </div>

        <div class="overflow-x-auto">
            @php
                $tableHeaders = ['No', 'Tahun', 'Bulan', 'Masalah/Kendala', 'Solusi'];
                if(isset($kolomDinamis)) {
                    foreach($kolomDinamis as $k) {
                        $tableHeaders[] = $k->nama_kolom;
                    }
                }
                $tableHeaders[] = 'Aksi';
            @endphp

            <x-table :headers="$tableHeaders">
                @forelse($dataMasalah as $index => $row)
                    @php
                        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors text-xs">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center align-top">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center align-top">{{ $row->tahun }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center align-top">{{ $row->bulan }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-sm text-left align-top whitespace-pre-line">{{ $row->masalah_kendala }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-md text-left align-top whitespace-pre-line">{{ $row->solusi }}</td>
                        
                        <!-- ISI KOLOM DINAMIS -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-4 py-3 text-gray-600 text-center align-top font-medium">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                        Rp {{ $tambahan[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-4 py-3 text-center align-top">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editData({{ json_encode($row) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('masalah-kendala.destroy', $row->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data masalah / kendala.</td></tr>
                @endforelse
            </x-table>
            <div class="mt-4 px-4 pb-4">
                {{ $dataMasalah->links() }}
            </div>
        </div>
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data" message="Data yang dihapus tidak dapat dikembalikan. Lanjutkan?" />
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel dan formulir. Lanjutkan?" />

    <!-- MODAL IMPORT MASALAH KENDALA -->
    <x-import-modal 
        id="modalImportMasalah" 
        route="{{ route('masalah-kendala.import') }}" 
        title="Import Data Masalah Kendala" 
        templateRoute="{{ route('template.download', 'masalah-kendala') }}" 
    />

    <!-- ================= MODAL ATUR KOLOM ================= -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan" description="Kelola kolom ekstra khusus untuk modul Masalah & Kendala.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar Saat Ini:</h4>
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded transition-colors" title="Hapus Kolom">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach
            @else
                <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan yang dibuat.</p>
            @endif
        </div>

        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t border-gray-200 pt-5">
            @csrf
            <input type="hidden" name="modul" value="masalah_kendala">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Buat Kolom Baru:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kolom (Cth: PIC / Penanggung Jawab)</label>
                    <input type="text" name="nama_kolom" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option>
                        <option value="number">Angka Kuantitas Biasa</option>
                        <option value="currency">Harga / Uang (Titik Otomatis)</option>
                        <option value="date">Tanggal</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Selesai, Pending, Proses" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-5">
                <x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button>
                <x-button variant="primary" type="submit" class="!bg-blue-600 hover:!bg-blue-700 border-none">Simpan Kolom</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH ================= -->
    <x-modal id="modalTambah" title="Tambah Catatan Kendala" description="Masukkan masalah yang dihadapi beserta solusi yang telah dilakukan.">
        <form action="{{ route('masalah-kendala.store') }}" method="POST" id="formTambah" class="grid grid-cols-1 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_tambah" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'add_tahun', 'add_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="add_tahun">
                <input type="hidden" name="bulan" id="add_bulan">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Masalah / Kendala <span class="text-red-500">*</span></label>
                <textarea name="masalah_kendala" id="add_masalah" rows="3" required placeholder="Jelaskan masalah yang terjadi..." class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Masalah wajib diisi!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Solusi / Tindak Lanjut <span class="text-red-500">*</span></label>
                <textarea name="solusi" id="add_solusi" rows="3" required placeholder="Jelaskan solusi atau tindak lanjutnya..." class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Solusi wajib diisi!</span>
            </div>

            <!-- AREA KOLOM DINAMIS (TAMBAH) -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'text')
                            <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'number')
                            <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">Rp</span></div>
                                <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-currency w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none" placeholder="0">
                            </div>
                        @elseif($kolom->tipe_input === 'date')
                            <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                                <option value="">Pilih...</option>
                                @if($kolom->pilihan_dropdown)
                                    @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)
                                        <option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @endif
                    </div>
                @endforeach
            @endif

            <div class="flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambah')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL EDIT ================= -->
    <x-modal id="modalEdit" title="Edit Catatan Kendala" description="Perbarui rincian masalah maupun solusinya.">
        <form action="" method="POST" id="formEdit" class="grid grid-cols-1 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_edit" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'edit_tahun', 'edit_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="edit_tahun">
                <input type="hidden" name="bulan" id="edit_bulan">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Masalah / Kendala <span class="text-red-500">*</span></label>
                <textarea name="masalah_kendala" id="edit_masalah" rows="3" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Masalah wajib diisi!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Solusi / Tindak Lanjut <span class="text-red-500">*</span></label>
                <textarea name="solusi" id="edit_solusi" rows="3" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Solusi wajib diisi!</span>
            </div>

            <!-- AREA KOLOM DINAMIS (EDIT) -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'text')
                            <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'number')
                            <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">Rp</span></div>
                                <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-edit w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none" placeholder="0">
                            </div>
                        @elseif($kolom->tipe_input === 'date')
                            <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                                <option value="">Pilih...</option>
                                @if($kolom->pilihan_dropdown)
                                    @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)
                                        <option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @endif
                    </div>
                @endforeach
            @endif

            <div class="flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEdit')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // --- DROPDOWN MENU ACTION BAR LOGIC ---
    function toggleActionDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) { el.classList.remove('hidden'); }
    }

    // Tutup dropdown jika user klik sembarang tempat di luar kotak
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    // PENCEGAH BENTROK MODAL
    function triggerDeleteKolom(deleteUrl) {
        closeModal('modalAturKolom');
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
    }

    function toggleDropdownConfig() {
        const selector = document.getElementById('tipeInputSelector');
        const configArea = document.getElementById('dropdownConfigArea');
        if(selector.value === 'dropdown') {
            configArea.classList.remove('hidden');
            configArea.querySelector('input').setAttribute('required', 'true');
        } else {
            configArea.classList.add('hidden');
            configArea.querySelector('input').removeAttribute('required');
            configArea.querySelector('input').value = '';
        }
    }

    // ================= Parsing Kalendar Bulan =================
    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    
    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
            
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
            return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`;
        }
        return '';
    }

    // ================= Aksi Buka Modal =================
    function openModalTambah() {
        const form = document.getElementById('formTambah');
        form.reset();
        
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah').value = currentMonth;
        syncPeriode(currentMonth, 'add_tahun', 'add_bulan');
        
        form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        openModal('modalTambah');
    }

    function editData(row) {
        const form = document.getElementById('formEdit');
        form.action = `/masalah-kendala/${row.id}`;
        
        const pickerVal = getMonthPickerValue(row.tahun, row.bulan);
        document.getElementById('picker_edit').value = pickerVal;
        syncPeriode(pickerVal, 'edit_tahun', 'edit_bulan');

        document.getElementById('edit_masalah').value = row.masalah_kendala;
        document.getElementById('edit_solusi').value = row.solusi;
        
        // Auto-fill Data Dinamis Edit
        const tambahan = typeof row.data_tambahan === 'string' ? JSON.parse(row.data_tambahan) : (row.data_tambahan || {});
        document.querySelectorAll('.input-dinamis-edit').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });
        
        form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        openModal('modalEdit');
    }

    // ================= Auto-Dot Formatter Currency =================
    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-currency')) {
            let value = e.target.value.replace(/[^,\d]/g, '');
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if(ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
            e.target.value = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }
    });

    // ================= Validasi Modal (Wajib Diisi Teks Merah) =================
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

    // Auto-open modal atur kolom jika error duplikat nama kolom
    @if($errors->has('nama_kolom') || $errors->has('tipe_input'))
        document.addEventListener('DOMContentLoaded', function() {
            openModal('modalAturKolom');
        });
    @endif
</script>
@endsection
