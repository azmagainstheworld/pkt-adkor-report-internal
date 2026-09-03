@extends('layouts.app')

@section('content')
<style>
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>
<!-- Tambahkan Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <x-success-modal />

    <!-- ================= MODAL ERROR KUSTOM ================= -->
        <!-- ================= MODAL ERROR KUSTOM & VALIDASI ================= -->
    @if (session('error_modal') || $errors->any())
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Peringatan Sistem</h3>
            <div class="text-sm text-gray-500 mb-6 text-left">
                @if(session('error_modal'))
                    <p class="text-center">{{ session('error_modal') }}</p>
                @else
                    <p class="font-bold text-gray-700 mb-2">Gagal memproses data. Periksa inputan Anda:</p>
                    <ul class="list-disc ml-5 text-xs text-red-500 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <button onclick="document.getElementById('errorModalCustom').remove()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                Kembali & Perbaiki
            </button>
        </div>
    </div>
    @endif

    

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Jasa Kurir</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Jasa Kurir</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
        
        <!-- Filter Kanan Atas (Dinamis dari Database) -->
        <form action="{{ route('jasakurir') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer">
                <option value="semua" {{ $tahunFilter == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $tahunFilter == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer">
                <option value="semua" {{ $bulanFilter == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach($availableMonths as $m)
                    <option value="{{ $m }}" {{ $bulanFilter == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART SECTION ================= -->
        <div class="mb-8">
        <x-dynamic-chart 
            id="kurirChart" 
            title="Statistik Penggunaan Jasa Kurir" 
            subtitle="Total volume pengiriman dokumen/barang per ekspedisi (Tahun: {{ $tahunFilter }})" 
            type="bar" 
        />
    </div>

    <!-- ================= DATA TABLE SECTION ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white">
        
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-wrap gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Detail Pengiriman per Ekspedisi</h3>
                <p class="text-xs text-gray-400">Data rincian jumlah layanan yang digunakan</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="relative dropdown-container">
                    <x-button variant="outline" type="button" onclick="toggleActionDropdown('dropdownKurir')" class="!rounded-xl !py-2 shadow-sm text-xs font-medium text-blue-600 border-blue-200 hover:bg-blue-50 flex items-center gap-1.5 min-w-[140px] justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </x-button>

                    <div id="dropdownKurir" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 z-50 py-2 origin-top-right transition-all duration-200">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">Ekspor & Impor</span>
                        </div>
                        <a href="javascript:void(0)" onclick="openModal('modalImportKurir'); document.getElementById('dropdownKurir').classList.add('hidden')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import dari Excel
                        </a>
                        <a href="{{ route('jasakurir.export.excel', request()->query()) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel (Rekap)
                        </a>
                        <a href="{{ route('jasakurir.export.pdf', request()->query()) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export Laporan PDF
                        </a>
                        @if(auth()->user()->isAdmin())
@if(auth()->check() && auth()->user()->isAdmin())
<div class="px-4 py-2 border-y border-gray-100 mt-1 bg-gray-50/50">
                            <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">Konfigurasi</span>
                        </div>
                        <a href="javascript:void(0)" onclick="openModal('modalAturKolom'); document.getElementById('dropdownKurir').classList.add('hidden')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                            Atur Kolom Tambahan
                        </a>
@endif
@endif
                    </div>
                </div>
                @if(auth()->user()->isAdmin())
<x-button variant="outline" onclick="openModal('modalMasterKurir')" class="!rounded-xl !py-2 shadow-sm text-xs font-medium text-gray-600 border-gray-300 hover:bg-gray-50 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Atur Ekspedisi
                </x-button>
@endif
                                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>
                <x-button variant="primary" onclick="bukaModalTambahData()" class="!bg-[#F7941E] hover:!bg-orange-600 border-none !rounded-xl !py-2 shadow-sm text-xs">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Data
                </x-button>
            </div>
        </div>

        @php
            $headers = ['<input type="checkbox" id="selectAllBulk" onclick="toggleSelectAll()">', 'Tahun', 'Bulan'];
            foreach($kurirMaster as $kurir) { $headers[] = strtoupper($kurir->nama_kurir); }
            $headers[] = 'Total';
            // Inject Header Kolom Dinamis
            if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $headers[] = $k->nama_kolom; } }
            // HEADER AKSI
            $headers[] = 'Aksi'; 
        @endphp

                <form id="bulkDeleteForm" action="{{ route('jasakurir.data.destroyBulk') }}" method="POST">
            <input type="hidden" id="deleteAllPages" name="delete_all_pages" value="0">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulk" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulk" class="hide-bulk overflow-x-auto">
            <x-table :headers="$headers">
                @forelse($tableData as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-100 last:border-0">
                        <td class="px-6 py-4 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk" value="{{ $row['tahun'] }}|{{ $row['bulan'] }}" onclick="toggleCheckbox()"></td>
                        <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">{{ $row['tahun'] }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium whitespace-nowrap">{{ $row['bulan'] }}</td>
                        
                        @foreach($kurirMaster as $kurir)
                            <td class="px-6 py-4 text-gray-600 text-center">{{ $row['kurir_'.$kurir->id] ?? 0 }}</td>
                        @endforeach
                        
                        <td class="px-6 py-4 font-bold text-blue-900 bg-blue-50 text-center">{{ $row['total_semua'] }}</td>

                        <!-- RENDER ISI KOLOM DINAMIS -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-6 py-4 text-gray-600 font-medium text-center align-middle">
                                    @if($kolom->tipe_input === 'currency' && isset($row['data_tambahan'][$kolom->nama_kolom]))
                                        Rp {{ $row['data_tambahan'][$kolom->nama_kolom] }}
                                    @else
                                        {{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <!-- KOLOM AKSI (EDIT & HAPUS) -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editDataKurir({{ json_encode($row) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data Bulan Ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <!-- Form Hapus via Route Name Delete Data -->
                                <form action="{{ route('jasakurir.data.destroy', ['tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh data pengiriman bulan {{ $row['bulan'] }} {{ $row['tahun'] }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data Bulan Ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-gray-500">Belum ada data pengiriman untuk filter ini.</td></tr>
                @endforelse
            </x-table>
            </div>
        </form>
        <!-- PAGINATION TABEL JASA KURIR -->
        @if(method_exists($tableData, 'links'))
            <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                {{ $tableData->links() }}
            </div>
        @endif
    </x-card>

    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari sistem. Lanjutkan?" />

    <!-- ================= MODAL IMPORT ================= -->
    <x-modal id="modalImportKurir" title="Import Data Jasa Kurir">
        <form action="{{ route('jasakurir.import') }}" method="POST" enctype="multipart/form-data" id="formImportKurir" class="space-y-4">
            @csrf
            
            <div class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-xl text-sm mb-4">
                <p class="font-bold mb-1">Panduan Import:</p>
                <ul class="list-disc list-inside">
                    <li>Unduh template yang disediakan untuk melihat format yang benar.</li>
                    <li>Jangan mengubah nama kolom (header) pada baris pertama.</li>
                    <li>Pastikan nama ekspedisi sesuai dengan yang ada di sistem.</li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('jasakurir.download.template') }}" class="inline-flex items-center gap-1.5 text-blue-600 font-bold hover:text-blue-800 underline underline-offset-2">
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
                <x-button variant="outline" type="button" onclick="closeModal('modalImportKurir')" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
                <x-button variant="primary" type="submit" form="formImportKurir" class="!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600 border-none">Import Data</x-button>
            </x-slot>
        </form>
    </x-modal>
    <x-delete-modal id="modalHapusMaster" title="Hapus Ekspedisi" message="Apakah Anda yakin ingin menghapus ekspedisi ini dari sistem? Pastikan tidak ada data yang terkait." />

    <!-- ================= MODAL ATUR KOLOM ================= -->
    @if(auth()->user()->isAdmin())
<x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan" description="Kelola kolom ekstra khusus untuk modul Jasa Kurir.">
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
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t border-gray-200 pt-5">
            @csrf <input type="hidden" name="modul" value="jasa_kurir">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Buat Kolom Baru:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option><option value="number">Angka Kuantitas Biasa</option><option value="currency">Harga / Uang (Rp)</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Selesai, Pending" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button><x-button variant="primary" type="submit" class="!bg-blue-600 hover:!bg-blue-700 border-none">Simpan Kolom</x-button></div>
        </form>
    </x-modal>
@endif

    <!-- ================= MODAL KELOLA MASTER KURIR ================= -->
    <x-modal id="modalMasterKurir" title="Pengaturan Ekspedisi" description="Kelola daftar jasa ekspedisi/kurir di dalam sistem.">
        
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 max-h-48 overflow-y-auto">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Ekspedisi Terdaftar:</h4>
            @if($kurirMaster->count() > 0)
                @foreach($kurirMaster as $kurir)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <span class="text-sm font-semibold text-gray-800">{{ strtoupper($kurir->nama_kurir) }}</span>
                        <button type="button" onclick="triggerDeleteMaster('{{ route('jasakurir.master.destroy', $kurir->id) }}')" class="text-red-500 p-1 hover:bg-red-50 rounded transition-colors" title="Hapus Ekspedisi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach
            @else
                <p class="text-xs text-gray-500 italic">Belum ada ekspedisi terdaftar.</p>
            @endif
        </div>

        <form action="{{ route('jasakurir.master.store') }}" method="POST" id="formMasterKurir" class="border-t border-gray-200 pt-5 space-y-5 novalidate-form" novalidate>
            @csrf
            <h4 class="text-sm font-bold text-gray-800 mb-2">Tambah Ekspedisi Baru:</h4>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Jasa Kurir / Ekspedisi <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kurir" required placeholder="Contoh: Ninja Xpress..." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Nama Ekspedisi wajib diisi!</span>
            </div>
        </form>

        <x-slot name="footer">
            <x-button variant="outline" onclick="closeModal('modalMasterKurir')" class="!px-6 !py-2.5 !rounded-lg">Tutup</x-button>
            <x-button variant="primary" type="submit" form="formMasterKurir" class="!px-6 !py-2.5 !rounded-lg !bg-blue-700 hover:!bg-blue-800 border-none">Simpan Ekspedisi</x-button>
        </x-slot>
    </x-modal>

    <!-- ================= MODAL TAMBAH DATA BULANAN ================= -->
    <x-modal id="modalTambahData" title="Input Data Pengiriman" description="Masukkan jumlah pengiriman untuk bulan laporan.">
        <form action="{{ route('jasakurir.data.store') }}" method="POST" id="formDataKurir" class="space-y-5 novalidate-form" novalidate>
            @csrf
            
            <!-- Menggunakan Input Kalender Month -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="periode_input_tambah" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500 cursor-pointer" onchange="syncPeriode(this.value, 'tahun_add', 'bulan_add')">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Periode wajib dipilih!</span>
                <!-- Hidden inputs -->
                <input type="hidden" name="tahun" id="tahun_add">
                <input type="hidden" name="bulan" id="bulan_add">
            </div>

            <hr class="border-gray-200 my-2">
            
            <h4 class="text-sm font-bold text-gray-800 mb-2">Jumlah Pengiriman per Ekspedisi</h4>
            <div class="grid grid-cols-2 gap-4">
                @foreach($kurirMaster as $kurir)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ strtoupper($kurir->nama_kurir) }}</label>
                        <input type="number" name="kurir[{{ $kurir->id }}]" min="0" placeholder="0" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                    </div>
                @endforeach
            </div>

            <!-- INJEKSI KOLOM DINAMIS (TAMBAH DATA) -->
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
            <x-button variant="outline" onclick="closeModal('modalTambahData')" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
            <x-button variant="primary" type="submit" form="formDataKurir" class="!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600 border-none">Simpan Data</x-button>
        </x-slot>
    </x-modal>

    <!-- ================= MODAL EDIT DATA ================= -->
    <x-modal id="modalEditData" title="Edit Data Pengiriman" description="Perbarui jumlah pengiriman dan rincian tambahan untuk bulan ini.">
        <form action="{{ route('jasakurir.data.store') }}" method="POST" id="formEditKurir" class="space-y-5 novalidate-form" novalidate>
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
            
            <h4 class="text-sm font-bold text-gray-800 mb-2">Jumlah Pengiriman per Ekspedisi</h4>
            <div class="grid grid-cols-2 gap-4">
                @foreach($kurirMaster as $kurir)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ strtoupper($kurir->nama_kurir) }}</label>
                        <!-- ID spesifik agar diisi otomatis oleh JS -->
                        <input type="number" name="kurir[{{ $kurir->id }}]" id="edit_kurir_{{ $kurir->id }}" min="0" placeholder="0" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                    </div>
                @endforeach
            </div>

            <!-- INJEKSI KOLOM DINAMIS (EDIT DATA) -->
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
            <x-button variant="primary" type="submit" form="formEditKurir" class="!px-6 !py-2.5 !rounded-lg !bg-amber-500 hover:!bg-amber-600 border-none">Update Data</x-button>
        </x-slot>
    </x-modal>

</main>

<!-- ================= JAVASCRIPT ================= -->
    <script>
        function toggleBulkMode() {
            let container = document.getElementById("tableContainerBulk");
            let btn = document.getElementById("btnModeBulk");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            let deleteAllInput = document.getElementById("deleteAllPages");
            if (deleteAllInput) { deleteAllInput.value = selectAll.checked ? "1" : "0"; }
            toggleDeleteBtn();
        }
        function toggleCheckbox() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn();
        }
        function toggleDeleteBtn() {
            let group = document.getElementById("btnGroupBulk");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll() {
            let selectAll = document.getElementById("selectAllBulk");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn();
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

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // Pencegah Bentrok Modal khusus Kolom Dinamis
    function triggerDeleteKolom(deleteUrl) {
        closeModal('modalAturKolom');
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
    }

    // Pencegah Bentrok Modal khusus Master Kurir (Ekspedisi)
    function triggerDeleteMaster(deleteUrl) {
        closeModal('modalMasterKurir');
        setTimeout(() => { openDeleteModal('modalHapusMaster', deleteUrl); }, 200);
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

    function bukaModalTambahData() {
        document.getElementById('formDataKurir').reset();
        
        // Default kalender ke bulan ini
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('periode_input_tambah').value = currentMonth;
        syncPeriode(currentMonth, 'tahun_add', 'bulan_add');

        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambahData');
    }

    // ================= AUTO-FILL EDIT DATA =================
    function editDataKurir(row) {
        document.getElementById('formEditKurir').reset();
        
        // Isi text Readonly
        document.getElementById('edit_tahun').value = row.tahun;
        document.getElementById('edit_bulan').value = row.bulan;

        // Isi jumlah masing-masing kurir
        for (const [kurirId, jumlah] of Object.entries(row.kurir_data)) {
            const input = document.getElementById('edit_kurir_' + kurirId);
            if (input) input.value = jumlah;
        }

        // Isi Kolom Dinamis (JSON)
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
                // 1. Strip leading zeros for all number inputs globally
        document.addEventListener('input', function(e) {
            if (e.target.type === 'number') {
                let val = e.target.value;
                if (val.length > 1 && val.startsWith('0') && !val.startsWith('0.')) {
                    e.target.value = val.replace(/^0+/, '');
                    if (e.target.value === '') e.target.value = '0';
                }
            }
        });

        const canvasKurir = document.getElementById('canvas_kurirChart');
        if (canvasKurir) {
            const ctx = canvasKurir.getContext('2d');
            const rawKurirMaster = {!! json_encode($kurirMaster) !!};
            const rawChartData = {!! json_encode($chartData) !!};

            const colorPalette = [
                '#0056A3', '#F7941E', '#EF4444', '#EAB308', 
                '#EC4899', '#9333EA', '#10B981', '#14B8A6',
                '#F97316', '#6366F1', '#8B5CF6', '#06B6D4'
            ];

            const labels = rawChartData.map(d => d.bulan.substring(0, 3));
            const datasets = rawKurirMaster.map((kurir, index) => {
                return {
                    label: kurir.nama_kurir,
                    data: rawChartData.map(d => d[kurir.nama_kurir] || 0),
                    backgroundColor: colorPalette[index % colorPalette.length],
                    borderRadius: 4,
                    borderSkipped: false,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                };
            });

                        window.chartInstances = window.chartInstances || {};
            window.changeChartType = window.changeChartType || function(id, newType) {
                if (window.chartInstances && window.chartInstances[id]) {
                    window.chartInstances[id].config.type = newType;
                    window.chartInstances[id].update();
                }
            };
            window.chartInstances['kurirChart'] = new Chart(ctx, {
                type: 'bar',
                data: { labels: labels, datasets: datasets },
                options: {
                    responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyle: 'rectRounded', font: { family: "'Poppins', sans-serif", size: 11 } } },
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
