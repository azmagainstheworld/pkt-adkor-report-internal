@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <x-success-modal />

    <!-- ================= MODAL ERROR KUSTOM (PENCEGAH DUPLIKAT KOLOM) ================= -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Peringatan Sistem</h3>
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
                <span class="font-bold">Gagal memproses data. Periksa inputan Anda:</span>
            </div>
            <ul class="list-disc list-inside pl-8 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Undangan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Undangan</h2>
            <p class="text-sm text-gray-500 font-medium">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
        </div>
        
        <!-- Filter Kanan Atas Dinamis -->
        <form action="{{ route('undangan.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-blue-500">
                <option value="semua" {{ $tahunFilter == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $tahunFilter == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-blue-500">
                <option value="semua" {{ $bulanFilter == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach($bulanTersedia as $b)
                    <option value="{{ $b }}" {{ $bulanFilter == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART SECTION ================= -->
    <x-card class="!rounded-xl overflow-visible shadow-sm border border-gray-100 mb-8 p-6 bg-white">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Statistik Distribusi Undangan per Bulan</h3>
                <p class="text-xs text-gray-400">
                    Perbandingan volume undangan intern dan ekstern 
                    {{ $tahunFilter == 'semua' ? 'Keseluruhan (Semua Tahun)' : 'Tahun ' . $tahunFilter }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#0056A3]"></span><span class="text-gray-700 font-medium">Undangan Intern</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#F7941E]"></span><span class="text-gray-700 font-medium">Undangan Ekstern</span></div>
            </div>
        </div>
        <div class="h-64 w-full relative"><canvas id="undanganChart"></canvas></div>
    </x-card>

    <!-- ================= TABEL REKAPITULASI ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Rekapitulasi Undangan Intern & Ekstern</h3>
                <p class="text-xs text-gray-400">Rincian jumlah undangan per periode bulanan</p>
            </div>
            
            <div class="flex gap-3">
                <div class="relative dropdown-container">
                    <x-button variant="outline" type="button" onclick="toggleActionDropdown('dropdownUndangan')" class="!rounded-xl !py-2 shadow-sm text-xs font-medium text-blue-600 border-blue-200 hover:bg-blue-50 flex items-center gap-1.5 min-w-[140px] justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </x-button>

                    <div id="dropdownUndangan" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 z-50 py-2 origin-top-right transition-all duration-200">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">Ekspor & Impor</span>
                        </div>
                        <a href="javascript:void(0)" onclick="openModal('modalImportUndangan'); document.getElementById('dropdownUndangan').classList.add('hidden')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import dari Excel
                        </a>
                        <a href="{{ route('undangan.export.excel', request()->query()) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel (Rekap)
                        </a>
                        <a href="{{ route('undangan.export.pdf', request()->query()) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export Laporan PDF
                        </a>
                        <div class="px-4 py-2 border-y border-gray-100 mt-1 bg-gray-50/50">
                            <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">Konfigurasi</span>
                        </div>
                        <a href="javascript:void(0)" onclick="openModal('modalAturKolom'); document.getElementById('dropdownUndangan').classList.add('hidden')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                            Atur Kolom Tambahan
                        </a>
                    </div>
                </div>
                <x-button variant="primary" onclick="bukaModalTambah()" class="!bg-[#F7941E] hover:!bg-orange-600 border-none !rounded-xl !py-2 shadow-sm text-xs">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Data
                </x-button>
            </div>
        </div>

        @php
            $headers = ['Tahun', 'Bulan', 'Undangan Intern', 'Undangan Ekstern'];
            if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $headers[] = $k->nama_kolom; } }
            $headers[] = 'Aksi';
        @endphp

        <div class="overflow-x-auto">
            <x-table :headers="$headers">
                @forelse($tableData as $index => $row)
                    @php $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []); @endphp
                    <tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-100 last:border-0">
                        <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">{{ $row->tahun }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium whitespace-nowrap">{{ $row->bulan }}</td>
                        <td class="px-6 py-4 text-gray-700 font-semibold">{{ $row->undangan_intern }} Berkas</td>
                        <td class="px-6 py-4 text-gray-700 font-semibold">{{ $row->undangan_ekstern }} Berkas</td>
                        
                        <!-- RENDER ISI KOLOM DINAMIS -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-6 py-4 text-gray-600 font-medium align-middle">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                        Rp {{ $tambahan[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <!-- KOLOM AKSI EDIT DAN HAPUS -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editDataUndangan({{ json_encode($row) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('undangan.destroy', $row->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-gray-500">Tidak ada data untuk filter yang dipilih.</td></tr>
                @endforelse

                @if(count($tableData) > 0)
                <tr class="bg-gray-100 font-bold text-gray-900 text-sm border-t border-gray-200">
                    <td class="px-6 py-4 text-right" colspan="2">Grand Total :</td>
                    <td class="px-6 py-4 text-pkt-biru">{{ $totalIntern }} Berkas</td>
                    <td class="px-6 py-4 text-pkt-biru">{{ $totalEkstern }} Berkas</td>
                    @if(isset($kolomDinamis)) <td colspan="{{ count($kolomDinamis) + 1 }}"></td> @else <td></td> @endif
                </tr>
                @endif
            </x-table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel dan formulir. Lanjutkan?" />

    <!-- ================= MODAL IMPORT ================= -->
    <x-modal id="modalImportUndangan" title="Import Data Undangan">
        <form action="{{ route('undangan.import') }}" method="POST" enctype="multipart/form-data" id="formImportUndangan" class="space-y-4">
            @csrf
            
            <div class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-xl text-sm mb-4">
                <p class="font-bold mb-1">Panduan Import:</p>
                <ul class="list-disc list-inside">
                    <li>Unduh template yang disediakan untuk melihat format yang benar.</li>
                    <li>Jangan mengubah nama kolom (header) pada baris pertama.</li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('undangan.download.template') }}" class="inline-flex items-center gap-1.5 text-blue-600 font-bold hover:text-blue-800 underline underline-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Template Excel
                    </a>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">File Excel (.xlsx, .xls) <span class="text-red-500">*</span></label>
                <input type="file" name="file" accept=".xlsx, .xls" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 focus:outline-none focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Ukuran maksimal file: 5MB</p>
            </div>

            <x-slot name="footer">
                <x-button variant="outline" type="button" onclick="closeModal('modalImportUndangan')" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
                <x-button variant="primary" type="submit" form="formImportUndangan" class="!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600 border-none">Import Data</x-button>
            </x-slot>
        </form>
    </x-modal>

    <!-- ================= BLUEPRINT: MODAL ATUR KOLOM ================= -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan" description="Kelola kolom ekstra khusus untuk modul Undangan.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 max-h-48 overflow-y-auto">
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
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1 hover:bg-red-50 rounded transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan yang dibuat.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-5 space-y-4">
            @csrf <input type="hidden" name="modul" value="undangan">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Buat Kolom Baru:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option><option value="number">Angka Kuantitas Biasa</option><option value="currency">Harga / Uang (Rp)</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Selesai, Pending" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button><x-button variant="primary" type="submit" class="!bg-blue-600 hover:!bg-blue-700 border-none">Simpan Kolom</x-button></div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH DATA (SEKALIGUS) ================= -->
    <x-modal id="modalTambah" title="Input Data Undangan" description="Pilih periode dan masukkan total undangan untuk bulan tersebut. (Bersifat overwrite)">
        <form action="{{ route('undangan.store') }}" method="POST" id="formTambah" class="space-y-5 novalidate-form" novalidate>
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="periode_input_tambah" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500 cursor-pointer" onchange="syncPeriode(this.value, 'tahun_add', 'bulan_add')">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="tahun_add">
                <input type="hidden" name="bulan" id="bulan_add">
            </div>

            <hr class="border-gray-200 my-2">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Undangan Intern <span class="text-red-500">*</span></label>
                    <input type="number" name="undangan_intern" min="0" required placeholder="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Undangan Ekstern <span class="text-red-500">*</span></label>
                    <input type="number" name="undangan_ekstern" min="0" required placeholder="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
                </div>
            </div>

            <!-- INJEKSI KOLOM DINAMIS -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                <hr class="border-gray-200 my-2">
                <h4 class="text-sm font-bold text-gray-800 mb-2">Informasi Tambahan</h4>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($kolomDinamis as $kolom)
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'currency')
                                <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-currency w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm font-mono outline-none" placeholder="0"><input type="hidden" name="data_tambahan[{{ $kolom->nama_kolom }}]"></div>
                            @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                                    <option value="">Pilih...</option>
                                    @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                                </select>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </form>

        <x-slot name="footer">
            <x-button variant="outline" onclick="closeModal('modalTambah')" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
            <x-button variant="primary" type="submit" form="formTambah" class="!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600 border-none">Simpan Data</x-button>
        </x-slot>
    </x-modal>

    <!-- ================= MODAL EDIT DATA ================= -->
    <x-modal id="modalEditData" title="Edit Data Undangan" description="Perbarui jumlah undangan untuk periode ini.">
        <form action="{{ route('undangan.store') }}" method="POST" id="formEdit" class="space-y-5 novalidate-form" novalidate>
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun</label>
                    <input type="text" name="tahun" id="edit_tahun" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-bold text-gray-500 cursor-not-allowed outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan</label>
                    <input type="text" name="bulan" id="edit_bulan" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-bold text-gray-500 cursor-not-allowed outline-none">
                </div>
            </div>

            <hr class="border-gray-200 my-2">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Undangan Intern <span class="text-red-500">*</span></label>
                    <input type="number" name="undangan_intern" id="edit_intern" min="0" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Undangan Ekstern <span class="text-red-500">*</span></label>
                    <input type="number" name="undangan_ekstern" id="edit_ekstern" min="0" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
                </div>
            </div>

            <!-- INJEKSI KOLOM DINAMIS (EDIT) -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                <hr class="border-gray-200 my-2">
                <h4 class="text-sm font-bold text-gray-800 mb-2">Informasi Tambahan</h4>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($kolomDinamis as $kolom)
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'currency')
                                <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-edit w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm font-mono outline-none" placeholder="0"><input type="hidden" name="data_tambahan[{{ $kolom->nama_kolom }}]"></div>
                            @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                                    <option value="">Pilih...</option>
                                    @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                                </select>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </form>

        <x-slot name="footer">
            <x-button variant="outline" onclick="closeModal('modalEditData')" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
            <x-button variant="primary" type="submit" form="formEdit" class="!px-6 !py-2.5 !rounded-lg !bg-amber-500 hover:!bg-amber-600 border-none">Update Data</x-button>
        </x-slot>
    </x-modal>

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // Pencegah Bentrok Modal khusus Kolom Dinamis
    function triggerDeleteKolom(deleteUrl) {
        closeModal('modalAturKolom');
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
    }
    
    function toggleDropdownConfig() {
        const selector = document.getElementById('tipeInputSelector');
        const configArea = document.getElementById('dropdownConfigArea');
        if(selector.value === 'dropdown') {
            configArea.classList.remove('hidden'); configArea.querySelector('input').setAttribute('required', 'true');
        } else {
            configArea.classList.add('hidden'); configArea.querySelector('input').removeAttribute('required');
        }
    }

    // ================= FUNGSI SINKRONISASI KALENDER BULAN =================
    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    
    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
            
            if(event && event.target) {
                const picker = event.target;
                picker.classList.remove('border-red-500');
                picker.classList.add('border-gray-300');
                const err = picker.nextElementSibling;
                if(err && err.classList.contains('error-msg')) err.classList.add('hidden');
            }
        } else {
            document.getElementById(yearId).value = '';
            document.getElementById(monthId).value = '';
        }
    }

    // Toggle action dropdown function
    function toggleActionDropdown(id) {
        const dropdown = document.getElementById(id);
        const allDropdowns = document.querySelectorAll('[id^="dropdown"]');
        allDropdowns.forEach(drop => {
            if (drop.id !== id && !drop.classList.contains('hidden')) {
                drop.classList.add('hidden');
            }
        });
        dropdown.classList.toggle('hidden');
    }

    // Close dropdowns when clicking outside
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-container')) {
            document.querySelectorAll('[id^="dropdown"]').forEach(drop => {
                drop.classList.add('hidden');
            });
        }
    });

    function bukaModalTambah() {
        document.getElementById('formTambah').reset();
        
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('periode_input_tambah').value = currentMonth;
        syncPeriode(currentMonth, 'tahun_add', 'bulan_add');

        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambah');
    }

    // ================= AUTO-FILL EDIT DATA =================
    function editDataUndangan(row) {
        document.getElementById('formEdit').reset();
        
        document.getElementById('edit_tahun').value = row.tahun;
        document.getElementById('edit_bulan').value = row.bulan;
        document.getElementById('edit_intern').value = row.undangan_intern;
        document.getElementById('edit_ekstern').value = row.undangan_ekstern;

        const tambahan = typeof row.data_tambahan === 'string' ? JSON.parse(row.data_tambahan) : (row.data_tambahan || {});
        document.querySelectorAll('.input-dinamis-edit').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });

        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalEditData');
    }

    // ================= AUTO-DOT FORMATTER & PENCEGAH 'E' =================
    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-currency')) {
            let rawValue = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?!$)/, '');
            if(e.target.nextElementSibling && e.target.nextElementSibling.tagName === 'INPUT') {
                e.target.nextElementSibling.value = rawValue;
            }
            if (rawValue) {
                e.target.value = new Intl.NumberFormat('id-ID').format(rawValue);
            } else {
                e.target.value = '';
            }
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.target && e.target.type === 'number') {
            if (['e', 'E', '+', '-', '.'].includes(e.key)) { e.preventDefault(); }
        }
    });

    // ================= VALIDASI FORM CLIENT-SIDE TEXT MERAH =================
    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500'); field.classList.remove('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500'); field.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
            if (!isValid) e.preventDefault();
        });

        form.querySelectorAll('[required]').forEach(field => {
            ['input', 'change'].forEach(evt => {
                field.addEventListener(evt, function() {
                    const errorSpan = this.nextElementSibling;
                    if (this.value && this.value.trim() !== '') {
                        this.classList.remove('border-red-500'); this.classList.add('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                    }
                });
            });
        });
    });

    @if($errors->has('nama_kolom') || $errors->has('tipe_input'))
        document.addEventListener('DOMContentLoaded', function() {
            openModal('modalAturKolom');
        });
    @endif

    // ================= LOGIKA CHART.JS =================
    document.addEventListener('DOMContentLoaded', function() {
        const canvasUndangan = document.getElementById('undanganChart');
        if (canvasUndangan) {
            const ctx = canvasUndangan.getContext('2d');
            const rawChartData = {!! json_encode($chartData) !!};

            const labels = rawChartData.map(d => d.label);
            const dataIntern = rawChartData.map(d => d.intern);
            const dataEkstern = rawChartData.map(d => d.ekstern);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Undangan Intern', data: dataIntern, backgroundColor: '#0056A3', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                        { label: 'Undangan Ekstern', data: dataEkstern, backgroundColor: '#F7941E', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false }, 
                        tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', titleFont: { size: 13, family: "'Poppins', sans-serif" }, bodyFont: { size: 12, family: "'Poppins', sans-serif" }, padding: 12, cornerRadius: 8 }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#F3F4F6', drawBorder: false }, ticks: { font: { family: "'Poppins', sans-serif", size: 11 } } },
                        x: { grid: { display: false, drawBorder: false }, ticks: { font: { family: "'Poppins', sans-serif", size: 11 } } }
                    }
                }
            });
        }
    });
</script>
@endsection
