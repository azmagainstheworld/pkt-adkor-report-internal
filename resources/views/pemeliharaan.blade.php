@extends('layouts.app')

@section('content')
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    <!-- ================= MODAL ERROR KUSTOM ================= -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Peringatan Sistem</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none'; @if(session('failed_modul') == 'pemeliharaan_peralatan') openModal('modalAturKolomPeralatan'); @else openModal('modalAturKolomRutin'); @endif" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
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
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Pemeliharaan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pemeliharaan Peralatan & Furnitur</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('pemeliharaan.index') }}" method="GET" id="filterForm" class="flex items-center gap-3">
            <select name="tahun" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-orange-500">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>Tahun {{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-orange-500">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>Bulan {{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART DINAMIS ================= -->
    <div class="mb-8 h-96 relative z-10">
        <x-dynamic-chart title="Statistik Pemeliharaan & Investasi Rutin" subtitle="Distribusi jumlah kegiatan pemeliharaan per bulan" type="bar" id="pemeliharaanChart" />
        
        
    </div>

    <!-- ================= TABEL 1: PEMELIHARAAN RUTIN ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8 z-20 relative">
        <div class="p-5 border-b border-gray-100 bg-white flex flex-col xl:flex-row xl:items-center justify-between gap-4 rounded-t-xl z-[100] relative">
            <h3 class="font-bold text-gray-900 text-lg">Pemeliharaan dan Penyediaan Furniture serta Peralatan Kantor</h3>
            <div class="flex items-center gap-3 self-end xl:self-auto">
                
                <div class="relative inline-block text-left overflow-visible z-[100]">
                    <button type="button" onclick="toggleDropdown('dropdownOpsiRutin')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg> Opsi Lanjutan <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="dropdownOpsiRutin" class="hidden absolute right-0 mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all z-[100]">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 rounded-t-xl"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor & Impor</p></div>
                        <div class="py-1">
                            <button type="button" onclick="openModal('modalImportRutin'); toggleDropdown('dropdownOpsiRutin')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import dari Excel</button>
                            <a href="{{ route('pemeliharaan-rutin.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium border-t border-gray-50"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Export Excel</a>
                            <a href="{{ route('pemeliharaan-rutin.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-red-50 flex items-center gap-2 font-medium border-t border-gray-50"><svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Export Laporan PDF</a>
                        </div>
                        @if(auth()->check() && auth()->user()->isAdmin())
<div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi</p></div>
                        <div class="py-1">
                            <button type="button" onclick="openModal('modalMasterRutin'); toggleDropdown('dropdownOpsiRutin')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg> Atur Dokumen Kegiatan</button>
                            @if(auth()->check() && auth()->user()->isAdmin())
<button type="button" onclick="openModal('modalAturKolomRutin'); toggleDropdown('dropdownOpsiRutin')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium border-t border-gray-50"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom Tambahan</button>
@endif
                        </div>
@endif
                    </div>
                </div>

                                <button type="button" id="btnModeBulkRutin" onclick="toggleBulkModeRutin()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus semua
                </button>
                <x-button variant="primary" onclick="openModalTambahRutin()" class="!py-2 text-xs bg-[#F7941E] hover:bg-orange-600 border-none">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Data
                </x-button>
            </div>
        </div>

                <form id="bulkDeleteFormRutin" action="{{ route('pemeliharaan-rutin.destroyBulk') }}" method="POST">
              <input type="hidden" name="filter_tahun" value="{{ request('tahun', 'semua') }}">
              <input type="hidden" name="filter_bulan" value="{{ request('bulan', 'semua') }}">
              <input type="hidden" name="delete_all" id="deleteAllRutin" value="0">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulkRutin" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAllRutin()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulkRutin" class="hide-bulk overflow-x-auto">
            @php
                $headersRutin = ['<input type="checkbox" id="selectAllBulkRutin" onclick="toggleSelectAllRutin()">', 'Tahun', 'Bulan'];
                foreach($masterRutin as $master) { $headersRutin[] = $master->nama_pemeliharaan; }
                if(isset($kolomRutin)) { foreach($kolomRutin as $k) { $headersRutin[] = $k->nama_kolom; } }
                $headersRutin[] = 'Aksi';
            @endphp
            <x-table :headers="$headersRutin">
                @forelse($dataRutinTablePaginated as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk-rutin" value="{{ $row['tahun'] }}|{{ $row['bulan'] }}" onclick="toggleCheckboxRutin()"></td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        @foreach($masterRutin as $master)
                            <td class="px-4 py-3 text-gray-600 text-center font-semibold bg-gray-50/50 border-x border-gray-100">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach
                        @if(isset($kolomRutin))
                            @foreach($kolomRutin as $kolom)
                                <td class="px-4 py-3 text-gray-600 font-medium text-center align-middle">
                                    @if($kolom->tipe_input === 'currency' && isset($row['data_tambahan'][$kolom->nama_kolom])) Rp {{ $row['data_tambahan'][$kolom->nama_kolom] }}
                                    @else {{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }} @endif
                                </td>
                            @endforeach
                        @endif
                        <td class="px-4 py-3 text-center border-l border-gray-100">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editRutin('{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ json_encode($row['items']) }}', '{{ json_encode($row['data_tambahan'] ?? []) }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
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
            <div class="mt-4 px-4 pb-4">
                {{ $dataRutinTablePaginated->links() }}
            </div>
            </div>
        </form>
    </x-card>

    <!-- MODAL HAPUS KONFIRMASI -->
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari sistem. Lanjutkan?" />
    <x-delete-modal id="modalHapusMaster" title="Hapus Data Master" message="Data Master (Dropdown) ini akan dihapus. Lanjutkan?" />
    <x-delete-modal id="modalHapusRutin" title="Hapus Pemeliharaan Rutin" message="Data pemeliharaan rutin pada bulan ini akan dihapus secara permanen." />
    

    <!-- ================= MODAL ATUR DOKUMEN (RUTIN) ================= -->
    <x-modal id="modalMasterRutin" title="Kelola Kegiatan Rutin" description="Daftar kegiatan untuk tabel Pemeliharaan Rutin.">
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
            @forelse($masterRutin as $m)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-sm font-semibold text-gray-800">{{ $m->nama_pemeliharaan }}</span>
                    <form action="{{ route('pemeliharaan-rutin.master.destroy', $m->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada daftar kegiatan. Tambahkan di bawah.</p>
            @endforelse
        </div>
        <form action="{{ route('pemeliharaan-rutin.master.store') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
            @csrf
            <input type="text" name="nama_pemeliharaan" required placeholder="Nama Kegiatan Baru..." class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
            <x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Tambah</x-button>
        </form>
    </x-modal>

    <!-- Modal Atur Kolom Rutin -->
    @if(auth()->user()->isAdmin())
<x-modal id="modalAturKolomRutin" title="Pengaturan Kolom (Tabel Rutin)">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 max-h-48 overflow-y-auto">
            @if(isset($kolomRutin) && $kolomRutin->count() > 0)
                @foreach($kolomRutin as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div><p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p><p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span></p></div>
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}', 'modalAturKolomRutin')" class="text-red-500 p-1 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="pemeliharaan_rutin">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelectorRutin" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('Rutin')">
                        <option value="text">Teks Singkat</option><option value="number">Angka Kuantitas Biasa</option><option value="currency">Harga / Uang (Rp)</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="col-span-2 hidden" id="dropdownConfigAreaRutin">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Selesai, Pending" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolomRutin')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>
@endif

    <!-- ================= IMPORT EXCEL MODALS ================= -->
    <x-import-modal id="modalImportRutin" route="{{ route('pemeliharaan-rutin.import') }}" title="Import Data Pemeliharaan Rutin" templateRoute="{{ route('template.download', 'pemeliharaan-rutin') }}" />
    

    <!-- ================= MODAL TAMBAH & EDIT RUTIN ================= -->
    <x-modal id="modalTambahRutin" title="Tambah Pemeliharaan Rutin" description="Masukkan jumlah kegiatan pada bulan tertentu.">
        <form action="{{ route('pemeliharaan-rutin.store') }}" method="POST" class="grid grid-cols-2 gap-x-6 gap-y-4">
            @csrf
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_tambah_rutin" required class="w-full px-4 py-2 border rounded-lg text-sm outline-none focus:border-orange-500" onchange="syncPeriode(this.value, 'rutin_tahun', 'rutin_bulan')">
                <input type="hidden" name="tahun" id="rutin_tahun"><input type="hidden" name="bulan" id="rutin_bulan">
            </div>
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kegiatan <span class="text-red-500">*</span></label>
                <select name="rutin_id" required class="w-full px-4 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
                    <option value="" disabled selected>Pilih Jenis...</option>
                    @foreach($masterRutin as $master)<option value="{{ $master->id }}">{{ $master->nama_pemeliharaan }}</option>@endforeach
                </select>
            </div>
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" required class="w-full px-4 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
            </div>
            @if(isset($kolomRutin)) @foreach($kolomRutin as $kolom)
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                    @if($kolom->tipe_input === 'dropdown')
                        <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                            <option value="">Pilih...</option>
                            @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                        </select>
                    @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                    @elseif(in_array($kolom->tipe_input, ['number', 'currency']))<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                    @else<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">@endif
                </div>
            @endforeach @endif
            <div class="col-span-2 flex justify-end gap-3 mt-4 border-t border-gray-100 pt-4"><x-button variant="outline" type="button" onclick="closeModal('modalTambahRutin')">Batal</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>

    <x-modal id="modalEditRutin" title="Edit Pemeliharaan Rutin" description="Perbarui seluruh data pemeliharaan rutin beserta periodenya.">
        <form action="{{ route('pemeliharaan-rutin.updateBulan') }}" method="POST" class="grid grid-cols-2 gap-x-4 gap-y-4 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="old_tahun" id="old_rutin_tahun"><input type="hidden" name="old_bulan" id="old_rutin_bulan">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_edit_rutin" required class="w-full px-3 py-1.5 border rounded-md text-xs outline-none focus:border-orange-500" onchange="syncPeriode(this.value, 'edit_rutin_tahun', 'edit_rutin_bulan')">
                <input type="hidden" name="tahun" id="edit_rutin_tahun"><input type="hidden" name="bulan" id="edit_rutin_bulan">
            </div>
            <div class="col-span-2 border-b border-gray-100 my-1"></div>
            @foreach($masterRutin as $master)
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 truncate" title="{{ $master->nama_pemeliharaan }}">{{ $master->nama_pemeliharaan }}</label>
                    <input type="number" name="items[{{ $master->id }}]" id="edit_rutin_item_{{ $master->id }}" value="0" class="w-full px-3 py-1.5 border rounded-md text-xs outline-none focus:border-orange-500">
                </div>
            @endforeach
            @if(isset($kolomRutin) && $kolomRutin->count() > 0)
                <div class="col-span-2 border-b border-gray-100 my-1"></div>
                @foreach($kolomRutin as $kolom)
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit-rutin w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                                <option value="">Pilih...</option>
                                @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                            </select>
                        @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit-rutin w-full px-3 py-1.5 border rounded-md text-xs outline-none focus:border-orange-500">
                        @elseif(in_array($kolom->tipe_input, ['number', 'currency']))<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit-rutin w-full px-3 py-1.5 border rounded-md text-xs outline-none focus:border-orange-500">
                        @else<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit-rutin w-full px-3 py-1.5 border rounded-md text-xs outline-none focus:border-orange-500">@endif
                    </div>
                @endforeach
            @endif
            <div class="col-span-2 flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4"><x-button variant="outline" type="button" onclick="closeModal('modalEditRutin')">Batal</x-button><x-button variant="primary" type="submit">Update Data</x-button></div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH & EDIT PERALATAN ================= -->
    <x-modal id="modalTambahPeralatan" title="Tambah Rincian Pemeliharaan Alat" description="Pilih jenis peralatan dan masukkan jumlahnya.">
        <form action="{{ route('pemeliharaan-peralatan.store') }}" method="POST" class="grid grid-cols-2 gap-x-6 gap-y-4">
            @csrf
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_tambah_peralatan" required class="w-full px-4 py-2 border rounded-lg text-sm outline-none focus:border-orange-500" onchange="syncPeriode(this.value, 'alat_tahun', 'alat_bulan')">
                <input type="hidden" name="tahun" id="alat_tahun"><input type="hidden" name="bulan" id="alat_bulan">
            </div>
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Pemeliharaan <span class="text-red-500">*</span></label>
                <select name="peralatan_id" required class="w-full px-4 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
                    <option value="" disabled selected>Pilih Jenis...</option>
                    @foreach($masterPeralatan as $master)<option value="{{ $master->id }}">{{ $master->nama_peralatan }}</option>@endforeach
                </select>
            </div>
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" required class="w-full px-4 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
            </div>
            @if(isset($kolomPeralatan)) @foreach($kolomPeralatan as $kolom)
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                    @if($kolom->tipe_input === 'dropdown')
                        <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                            <option value="">Pilih...</option>
                            @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                        </select>
                    @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                    @elseif(in_array($kolom->tipe_input, ['number', 'currency']))<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                    @else<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">@endif
                </div>
            @endforeach @endif
            <div class="col-span-2 flex justify-end gap-3 mt-4 border-t border-gray-100 pt-4"><x-button variant="outline" type="button" onclick="closeModal('modalTambahPeralatan')">Batal</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>

    

</main>

    <script>
        // TABEL 1 (RUTIN)
        function toggleBulkModeRutin() {
            let container = document.getElementById("tableContainerBulkRutin");
            let btn = document.getElementById("btnModeBulkRutin");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllRutin();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            
            let deleteAllInput = document.getElementById("deleteAllRutin");
            if (deleteAllInput) {
                deleteAllInput.value = selectAll.checked ? '1' : '0';
            }
            toggleDeleteBtnRutin();
        }
        function toggleCheckboxRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            
            let deleteAllInput = document.getElementById("deleteAllRutin");
            if (deleteAllInput) {
                deleteAllInput.value = '0';
            }
            toggleDeleteBtnRutin();
        }
        function toggleDeleteBtnRutin() {
            let group = document.getElementById("btnGroupBulkRutin");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-rutin:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnRutin();
        }

        // TABEL 2 (PERALATAN)
        function toggleBulkModePeralatan() {
            let container = document.getElementById("tableContainerBulkPeralatan");
            let btn = document.getElementById("btnModeBulkPeralatan");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAllPeralatan();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAllPeralatan() {
            let selectAll = document.getElementById("selectAllBulkPeralatan");
            let checkboxes = document.querySelectorAll(".cb-bulk-peralatan");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnPeralatan();
        }
        function toggleCheckboxPeralatan() {
            let selectAll = document.getElementById("selectAllBulkPeralatan");
            let checkboxes = document.querySelectorAll(".cb-bulk-peralatan");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnPeralatan();
        }
        function toggleDeleteBtnPeralatan() {
            let group = document.getElementById("btnGroupBulkPeralatan");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-peralatan:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllPeralatan() {
            let selectAll = document.getElementById("selectAllBulkPeralatan");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-peralatan");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnPeralatan();
        }

    
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    function toggleDropdown(id) { document.getElementById(id).classList.toggle('hidden'); }
    document.addEventListener('click', function(event) { if (!event.target.closest('.relative.inline-block')) { document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden')); } });

    function triggerDeleteKolom(deleteUrl, modalId) { closeModal(modalId); setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200); }
    function toggleDropdownConfig(type) {
        const selector = document.getElementById('tipeInputSelector' + type);
        const configArea = document.getElementById('dropdownConfigArea' + type);
        if(selector.value === 'dropdown') { configArea.classList.remove('hidden'); configArea.querySelector('input').setAttribute('required', 'true'); } 
        else { configArea.classList.add('hidden'); configArea.querySelector('input').removeAttribute('required'); }
    }

    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
        } else {
            document.getElementById(yearId).value = '';
            document.getElementById(monthId).value = '';
        }
    }

    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) { return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`; } return '';
    }

    function openModalTambahRutin() { 
        const countData = {{ count($masterRutin) }}; if (countData === 0) { alert('Silakan atur Dokumen Kegiatan terlebih dahulu melalui Opsi Lanjutan.'); return; }
        const now = new Date(); const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah_rutin').value = currentMonth; syncPeriode(currentMonth, 'rutin_tahun', 'rutin_bulan');
        openModal('modalTambahRutin'); 
    }
        function openModalTambahPeralatan() { 
        const countData = {{ count($masterPeralatan) }}; if (countData === 0) { alert('Silakan atur Dokumen Peralatan terlebih dahulu melalui Opsi Lanjutan.'); return; }
        const now = new Date(); const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah_peralatan').value = currentMonth; syncPeriode(currentMonth, 'alat_tahun', 'alat_bulan');
        openModal('modalTambahPeralatan'); 
    }

    function editRutin(tahun, bulan, itemsJson, tambahanJson) {
        document.getElementById('old_rutin_tahun').value = tahun; document.getElementById('old_rutin_bulan').value = bulan;
        const pickerVal = getMonthPickerValue(tahun, bulan); document.getElementById('picker_edit_rutin').value = pickerVal; syncPeriode(pickerVal, 'edit_rutin_tahun', 'edit_rutin_bulan');
        
        const items = JSON.parse(itemsJson);
        @foreach($masterRutin as $master) if(document.getElementById('edit_rutin_item_{{ $master->id }}')) document.getElementById('edit_rutin_item_{{ $master->id }}').value = items[{{ $master->id }}] || 0; @endforeach

        const tambahan = JSON.parse(tambahanJson || '{}');
        document.querySelectorAll('.input-dinamis-edit-rutin').forEach(el => { const key = el.getAttribute('data-key'); el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : ''; });
        openModal('modalEditRutin');
    }

    function editPeralatan(tahun, bulan, itemsJson, tambahanJson) {
        document.getElementById('old_alat_tahun').value = tahun; document.getElementById('old_alat_bulan').value = bulan;
        const pickerVal = getMonthPickerValue(tahun, bulan); document.getElementById('picker_edit_alat').value = pickerVal; syncPeriode(pickerVal, 'edit_alat_tahun', 'edit_alat_bulan');
        
        const items = JSON.parse(itemsJson);
        @foreach($masterPeralatan as $master) if(document.getElementById('edit_alat_item_{{ $master->id }}')) document.getElementById('edit_alat_item_{{ $master->id }}').value = items[{{ $master->id }}] || 0; @endforeach

        const tambahan = JSON.parse(tambahanJson || '{}');
        document.querySelectorAll('.input-dinamis-edit-peralatan').forEach(el => { const key = el.getAttribute('data-key'); el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : ''; });
        openModal('modalEditPeralatan');
    }

    document.addEventListener('input', function(e) {
        if (e.target.type === 'number') {
            e.target.value = e.target.value.replace(/^0+(?=\d)/, '');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('canvas_pemeliharaanChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const rawChartData = {!! json_encode($chartData) !!};
            const colors = {!! json_encode($chartColors) !!};
            const datasets = [];

            @foreach($masterRutin as $index => $master)
                datasets.push({
                    label: '{!! addslashes($master->nama_pemeliharaan) !!}',
                    data: rawChartData.map(d => d.items[{{ $master->id }}] || 0),
                    backgroundColor: colors[{{ $index }} % colors.length],
                    borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8
                });
            @endforeach

            window.chart_pemeliharaanChart = new Chart(ctx, {
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

    function changeChartType(id, type) { if(window['chart_' + id]) { window['chart_' + id].config.type = type; window['chart_' + id].update(); } }
</script>
@endsection
