@extends('layouts.app')

@section('content')
<style>
    .hide-bulk .cb-bulk, .hide-bulk .bulk-cb-header, .hide-bulk .cb-bulk-1, .hide-bulk .cb-bulk-2, .hide-bulk .bulk-cb-header-1, .hide-bulk .bulk-cb-header-2 { display: none !important; }
    .hide-bulk th:first-child, .hide-bulk td:first-child { width: 0; padding: 0; overflow: hidden; opacity: 0; }
</style>
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
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none'; @if(session('failed_modul') == 'pa_teknik_2') openModal('modalAturKolom2'); @elseif(session('failed_modul') == 'pa_teknik_1') openModal('modalAturKolom1'); @endif" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
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
                <span class="text-gray-400">/</span><span class="text-gray-500">Kearsipan</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">PA Teknik</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Teknikal File - PA Teknik</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('pa-teknik.index') }}" method="GET" class="flex items-center gap-3">
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

    <!-- ================= TABEL 1 ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-orange-50 rounded-t-xl flex justify-end items-center gap-3">
            <div class="relative inline-block text-left">
                <button type="button" onclick="toggleDropdown('dropdownOpsi1')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-orange-200 shadow-sm px-4 py-1.5 bg-white text-xs font-medium text-orange-700 hover:bg-orange-100 focus:outline-none transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Opsi Lanjutan
                    <svg class="w-3.5 h-3.5 ml-1 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="dropdownOpsi1" class="hidden absolute right-0 z-[50] mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                    
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor & Impor</p>
                    </div>
                    <div class="py-1">
                        <button type="button" onclick="openImportModal('tabel1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Import dari Excel
                        </button>
                        <a href="{{ route('pa-teknik.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium border-t border-gray-50">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Data Excel
                        </a>
                        <a href="{{ route('pa-teknik.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-red-50 flex items-center gap-2 font-medium border-t border-gray-50">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export Laporan PDF
                        </a>
                    </div>
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi Tabel 1</p>
                    </div>
                    <div class="py-1">
                        <button type="button" onclick="openModal('modalAturDokumen1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                            Atur Dokumen Master
                        </button>
                        @if(auth()->check() && auth()->user()->isAdmin())
<button type="button" onclick="openModal('modalAturKolom1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium border-t border-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Atur Kolom Tambahan
                        </button>
@endif
                    </div>
                </div>
            </div>

                            <button type="button" id="btnModeBulk1" onclick="toggleBulkMode1()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Semua
                </button>
                <x-button variant="primary" onclick="openModalTambah(1)" class="!py-1.5 !px-3 text-xs bg-orange-600 hover:bg-orange-700 border-none">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Data
            </x-button>
        </div>
                            <form id="bulkDeleteForm1" action="{{ route('pa-teknik.destroyBulk') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="filter_tahun" value="{{ $filterTahun }}">
                <input type="hidden" name="filter_bulan" value="{{ $filterBulan }}">
                <input type="hidden" name="delete_all" id="deleteAllFlag1" value="0">
                <input type="hidden" name="kelompok_tabel" value="1">
                
                <div id="btnGroupBulk1" class="hidden mb-3 bg-red-50 border border-red-200 p-3 rounded-xl flex items-center justify-between">
                    <span class="text-xs font-semibold text-red-700 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Data terpilih akan dihapus permanen.</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="cancelAll1()" class="px-3 py-1.5 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="button" onclick="submitBulkDelete1()" class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus Terpilih</button>
                    </div>
                </div>
            <div id="tableContainerBulk1" class="hide-bulk overflow-x-auto">
            @php
                $headTabel1 = ['<input type="checkbox" id="selectAllBulk1" class="bulk-cb-header-1" onclick="toggleSelectAll1()">', 'Tahun', 'Bulan'];
                foreach($masterTabel1 as $master) { $headTabel1[] = $master->nama_kegiatan; }
                if(isset($kolomTabel1)) { foreach($kolomTabel1 as $k) { $headTabel1[] = $k->nama_kolom; } }
                $headTabel1[] = 'Aksi';
            @endphp
            <x-table :headers="$headTabel1">
                @forelse($paginatedTable1 as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk-1" value="1|{{ $row['tahun'] }}|{{ $row['bulan'] }}" onclick="toggleCheckbox1()"></td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        @foreach($masterTabel1 as $master)
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach

                        <!-- RENDER KOLOM DINAMIS 1 -->
                        @if(isset($kolomTabel1))
                            @foreach($kolomTabel1 as $kolom)
                                <td class="px-4 py-3 text-gray-600 font-medium text-center align-middle">
                                    @if($kolom->tipe_input === 'currency' && isset($row['data_tambahan'][$kolom->nama_kolom]))
                                        Rp {{ $row['data_tambahan'][$kolom->nama_kolom] }}
                                    @else
                                        {{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                @php 
                                    $itemsJson = json_encode($row['items']); 
                                    $tambahanJson = json_encode($row['data_tambahan'] ?? []);
                                @endphp
                                <button type="button" onclick="editBulan1('{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}', '{{ $tambahanJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('pa-teknik.destroyBulan', ['kelompok_tabel' => 1, 'tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headTabel1) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
                
                @if(count($dataTable1) > 0)
                <tr class="bg-gray-100 font-bold text-xs whitespace-nowrap border-t-2 border-gray-300">
                    <td colspan="2" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>
                    @foreach($masterTabel1 as $master)
                        <td class="px-4 py-4 text-orange-700 text-center">{{ number_format($totalsTabel1[$master->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    @if(isset($kolomTabel1)) <td colspan="{{ count($kolomTabel1) + 1 }}"></td> @else <td></td> @endif
                </tr>
                @endif
            </x-table>
        </form>
        </div>
    </x-card>

    <!-- ================= TABEL 2 ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-orange-50 rounded-t-xl flex justify-end items-center gap-3">
            <div class="relative inline-block text-left">
                <button type="button" onclick="toggleDropdown('dropdownOpsi2')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-orange-200 shadow-sm px-4 py-1.5 bg-white text-xs font-medium text-orange-700 hover:bg-orange-100 focus:outline-none transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Opsi Lanjutan
                    <svg class="w-3.5 h-3.5 ml-1 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="dropdownOpsi2" class="hidden absolute right-0 z-[50] mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                    
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor & Impor</p>
                    </div>
                    <div class="py-1">
                        <button type="button" onclick="openImportModal('tabel2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import dari Excel
                        </button>
                        <a href="{{ route('pa-teknik.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium border-t border-gray-50">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Data Excel
                        </a>
                        <a href="{{ route('pa-teknik.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-red-50 flex items-center gap-2 font-medium border-t border-gray-50">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export Laporan PDF
                        </a>
                    </div>
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi Tabel 2</p>
                    </div>
                    <div class="py-1">
                        <button type="button" onclick="openModal('modalAturDokumen2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                            Atur Dokumen Master
                        </button>
                        @if(auth()->check() && auth()->user()->isAdmin())
<button type="button" onclick="openModal('modalAturKolom2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium border-t border-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Atur Kolom Tambahan
                        </button>
@endif
                    </div>
                </div>
            </div>

                            <button type="button" id="btnModeBulk2" onclick="toggleBulkMode2()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Semua
                </button>
                <x-button variant="primary" onclick="openModalTambah(2)" class="!py-1.5 !px-3 text-xs bg-orange-600 hover:bg-orange-700 border-none">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Data
            </x-button>
        </div>
                            <form id="bulkDeleteForm2" action="{{ route('pa-teknik.destroyBulk') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="filter_tahun" value="{{ $filterTahun }}">
                <input type="hidden" name="filter_bulan" value="{{ $filterBulan }}">
                <input type="hidden" name="delete_all" id="deleteAllFlag2" value="0">
                <input type="hidden" name="kelompok_tabel" value="2">
                
                <div id="btnGroupBulk2" class="hidden mb-3 bg-red-50 border border-red-200 p-3 rounded-xl flex items-center justify-between">
                    <span class="text-xs font-semibold text-red-700 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Data terpilih akan dihapus permanen.</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="cancelAll2()" class="px-3 py-1.5 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="button" onclick="submitBulkDelete2()" class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus Terpilih</button>
                    </div>
                </div>
            <div id="btnGroupBulk2" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll2()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>
            <div id="tableContainerBulk2" class="hide-bulk overflow-x-auto">
            @php
                $headTabel2 = ['<input type="checkbox" id="selectAllBulk2" class="bulk-cb-header-2" onclick="toggleSelectAll2()">', 'Tahun', 'Bulan'];
                foreach($masterTabel2 as $master) { $headTabel2[] = $master->nama_kegiatan; }
                if(isset($kolomTabel2)) { foreach($kolomTabel2 as $k) { $headTabel2[] = $k->nama_kolom; } }
                $headTabel2[] = 'Aksi';
            @endphp
            <x-table :headers="$headTabel2">
                @forelse($paginatedTable2 as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk-2" value="2|{{ $row['tahun'] }}|{{ $row['bulan'] }}" onclick="toggleCheckbox2()"></td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        @foreach($masterTabel2 as $master)
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach

                        <!-- RENDER KOLOM DINAMIS 2 -->
                        @if(isset($kolomTabel2))
                            @foreach($kolomTabel2 as $kolom)
                                <td class="px-4 py-3 text-gray-600 font-medium text-center align-middle">
                                    @if($kolom->tipe_input === 'currency' && isset($row['data_tambahan'][$kolom->nama_kolom]))
                                        Rp {{ $row['data_tambahan'][$kolom->nama_kolom] }}
                                    @else
                                        {{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                @php 
                                    $itemsJson = json_encode($row['items']); 
                                    $tambahanJson = json_encode($row['data_tambahan'] ?? []);
                                @endphp
                                <button type="button" onclick="editBulan2('{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}', '{{ $tambahanJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('pa-teknik.destroyBulan', ['kelompok_tabel' => 2, 'tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headTabel2) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
                
                @if(count($dataTable2) > 0)
                <tr class="bg-gray-100 font-bold text-xs whitespace-nowrap border-t-2 border-gray-300">
                    <td colspan="2" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>
                    @foreach($masterTabel2 as $master)
                        <td class="px-4 py-4 text-orange-700 text-center">{{ number_format($totalsTabel2[$master->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    @if(isset($kolomTabel2)) <td colspan="{{ count($kolomTabel2) + 1 }}"></td> @else <td></td> @endif
                </tr>
                @endif
            </x-table>
        </form>
        </div>
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data" message="Data di bulan ini akan dihapus secara permanen. Lanjutkan?" />
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel dan formulir. Lanjutkan?" />
    <x-delete-modal id="modalHapusMaster" title="Hapus Dokumen Master" message="Apakah Anda yakin ingin menghapus dokumen ini? Tindakan ini tidak dapat dibatalkan." />

    <!-- ================= MODAL ATUR DOKUMEN (TABEL 1) ================= -->
    <x-modal id="modalAturDokumen1" title="Kelola Master Dokumen (Tabel 1)" description="Hapus jenis dokumen yang tidak diperlukan pada Tabel 1.">
        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
            @forelse($masterTabel1 as $master)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-sm font-medium text-gray-700">{{ $master->nama_kegiatan }}</span>
                    <button type="button" onclick="triggerDeleteMaster('{{ route('pa-teknik.destroyMaster', $master->id) }}', 'modalAturDokumen1')" class="p-1.5 text-red-500 hover:bg-red-50 rounded" title="Hapus Dokumen">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada dokumen.</p>
            @endforelse
        </div>
        <div class="flex justify-between items-center mt-5 pt-4 border-t border-gray-100">
            <form action="{{ route('pa-teknik.storeMaster') }}" method="POST" class="flex gap-2 w-full mr-4">
                @csrf <input type="hidden" name="kelompok_tabel" value="1">
                <input type="text" name="nama_kegiatan" required placeholder="Nama dokumen baru..." class="w-full px-3 py-1.5 border rounded-lg text-xs outline-none focus:border-orange-500">
                <x-button variant="primary" type="submit" class="!py-1.5 !px-3 text-xs whitespace-nowrap bg-orange-600 hover:bg-orange-700 border-none">Tambah</x-button>
            </form>
            <x-button variant="outline" type="button" onclick="closeModal('modalAturDokumen1')">Tutup</x-button>
        </div>
    </x-modal>

    <!-- ================= MODAL ATUR DOKUMEN (TABEL 2) ================= -->
    <x-modal id="modalAturDokumen2" title="Kelola Master Dokumen (Tabel 2)" description="Hapus jenis dokumen yang tidak diperlukan pada Tabel 2.">
        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
            @forelse($masterTabel2 as $master)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-sm font-medium text-gray-700">{{ $master->nama_kegiatan }}</span>
                    <button type="button" onclick="triggerDeleteMaster('{{ route('pa-teknik.destroyMaster', $master->id) }}', 'modalAturDokumen2')" class="p-1.5 text-red-500 hover:bg-red-50 rounded" title="Hapus Dokumen">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada dokumen.</p>
            @endforelse
        </div>
        <div class="flex justify-between items-center mt-5 pt-4 border-t border-gray-100">
            <form action="{{ route('pa-teknik.storeMaster') }}" method="POST" class="flex gap-2 w-full mr-4">
                @csrf <input type="hidden" name="kelompok_tabel" value="2">
                <input type="text" name="nama_kegiatan" required placeholder="Nama dokumen baru..." class="w-full px-3 py-1.5 border rounded-lg text-xs outline-none focus:border-orange-500">
                <x-button variant="primary" type="submit" class="!py-1.5 !px-3 text-xs whitespace-nowrap bg-orange-600 hover:bg-orange-700 border-none">Tambah</x-button>
            </form>
            <x-button variant="outline" type="button" onclick="closeModal('modalAturDokumen2')">Tutup</x-button>
        </div>
    </x-modal>

    <!-- ================= MODAL IMPORT EXCEL ================= -->
    <x-import-modal 
        id="modalImportExcel" 
        route="{{ route('pa-teknik.import') }}" 
        title="Import Data PA Teknik" 
        templateRoute="{{ route('template.download', 'pa-teknik') }}" 
    />

    <!-- ================= MODAL ATUR KOLOM (TABEL 1) ================= -->
    @if(auth()->user()->isAdmin())
<x-modal id="modalAturKolom1" title="Pengaturan Kolom Tambahan (Tabel 1)" description="Kelola kolom ekstra khusus untuk Tabel 1.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 max-h-48 overflow-y-auto">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
            @if(isset($kolomTabel1) && $kolomTabel1->count() > 0)
                @foreach($kolomTabel1 as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span></p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}', 'modalAturKolom1')" class="text-red-500 p-1 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="pa_teknik_1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector1" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500" onchange="toggleDropdownConfig('1')">
                        <option value="text">Teks Singkat</option><option value="number">Angka Kuantitas Biasa</option><option value="currency">Harga / Uang (Rp)</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea1">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Selesai, Pending" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolom1')">Tutup</x-button><x-button variant="primary" type="submit" class="bg-orange-600 hover:bg-orange-700 border-none">Simpan</x-button></div>
        </form>
    </x-modal>
@endif

    <!-- ================= MODAL ATUR KOLOM (TABEL 2) ================= -->
    @if(auth()->user()->isAdmin())
<x-modal id="modalAturKolom2" title="Pengaturan Kolom Tambahan (Tabel 2)" description="Kelola kolom ekstra khusus untuk Tabel 2.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 max-h-48 overflow-y-auto">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
            @if(isset($kolomTabel2) && $kolomTabel2->count() > 0)
                @foreach($kolomTabel2 as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span></p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}', 'modalAturKolom2')" class="text-red-500 p-1 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="pa_teknik_2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector2" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500" onchange="toggleDropdownConfig('2')">
                        <option value="text">Teks Singkat</option><option value="number">Angka Kuantitas Biasa</option><option value="currency">Harga / Uang (Rp)</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea2">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Selesai, Pending" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolom2')">Tutup</x-button><x-button variant="primary" type="submit" class="bg-orange-600 hover:bg-orange-700 border-none">Simpan</x-button></div>
        </form>
    </x-modal>
@endif

    <!-- MODAL TAMBAH DINAMIS -->
    <x-modal id="modalTambah" title="Tambah Data Dokumen" description="Pilih jenis dokumen dan masukkan jumlahnya.">
        <form action="{{ route('pa-teknik.storeDokumen') }}" method="POST" id="formTambah" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="kelompok_tabel" id="add_kelompok">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_tambah" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'add_tahun', 'add_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="add_tahun">
                <input type="hidden" name="bulan" id="add_bulan">
            </div>
            
            <div class="md:col-span-2 border-t border-gray-100 my-1 pt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kegiatan/Dokumen <span class="text-red-500">*</span></label>
                <select name="master_id" id="add_master_id" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <option value="" disabled selected>Pilih Jenis...</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bidang ini wajib dipilih!</span>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" required placeholder="0" onfocus="if(this.value === '0') this.value = '';" oninput="this.value = this.value.replace(/^0+(?=\d)/, '');" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Jumlah wajib diisi!</span>
            </div>

            <div id="dynamic_add_fields_container" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4"></div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambah')">Batal</x-button>
                <x-button variant="primary" type="submit" class="bg-orange-600 hover:bg-orange-700 border-none">Simpan Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL EDIT BULAN -->
    <x-modal id="modalEditBulan" title="Edit Data Bulan" description="Perbarui seluruh data pada bulan terkait.">
        <form action="{{ route('pa-teknik.updateBulan') }}" method="POST" class="grid grid-cols-2 gap-x-4 gap-y-4 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="kelompok_tabel" id="edit_kelompok">

            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Periode Laporan</label>
                <input type="month" id="picker_edit" readonly class="w-full px-3 py-2 border border-gray-200 bg-gray-100 text-gray-500 rounded-md text-xs outline-none cursor-not-allowed font-medium">
                <p class="text-[10px] text-gray-400 mt-1">Periode tidak dapat diubah. Untuk bulan lain, silakan klik edit pada baris yang sesuai.</p>
                <input type="hidden" name="tahun" id="edit_tahun">
                <input type="hidden" name="bulan" id="edit_bulan">
            </div>
            <div class="col-span-2 border-b border-gray-100 my-1"></div>
            
            <div id="edit_dynamic_inputs" class="col-span-2 grid grid-cols-2 gap-4"></div>

            <div id="edit_tambahan_inputs_container" class="col-span-2">
                <div class="border-b border-gray-100 my-1"></div>
                <h4 class="text-sm font-bold text-gray-800 mb-3">Informasi Tambahan</h4>
                <div id="edit_tambahan_inputs" class="grid grid-cols-2 gap-4"></div>
            </div>
            
            <div class="col-span-2 flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEditBulan')">Batal</x-button>
                <x-button variant="primary" type="submit" class="bg-orange-600 hover:bg-orange-700 border-none">Update Data</x-button>
            </div>
        </form>
    </x-modal>
</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // ================= TOGGLE OPSI LANJUTAN =================
    function toggleDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) { el.classList.remove('hidden'); }
    }
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    // ================= BLUEPRINT: PENCEGAH BENTROK MODAL =================
    function triggerDeleteKolom(deleteUrl, modalId) {
        closeModal(modalId);
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
    }
    function triggerDeleteMaster(deleteUrl, modalId) {
        closeModal(modalId);
        setTimeout(() => { openDeleteModal('modalHapusMaster', deleteUrl); }, 200);
    }

    function toggleDropdownConfig(tableId) {
        const selector = document.getElementById('tipeInputSelector' + tableId);
        const configArea = document.getElementById('dropdownConfigArea' + tableId);
        if(selector.value === 'dropdown') {
            configArea.classList.remove('hidden'); configArea.querySelector('input').setAttribute('required', 'true');
        } else {
            configArea.classList.add('hidden'); configArea.querySelector('input').removeAttribute('required');
        }
    }

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
        }
    }
    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) {
            return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`;
        }
        return '';
    }

    const master1 = {!! json_encode($masterTabel1) !!};
    const master2 = {!! json_encode($masterTabel2) !!};

    const htmlKolom1 = `
        @if(isset($kolomTabel1) && $kolomTabel1->count() > 0)
            <div class="md:col-span-2 border-t border-gray-100 pt-3"><h4 class="text-sm font-bold text-gray-800 mb-3">Informasi Tambahan</h4></div>
            @foreach($kolomTabel1 as $kolom)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                    @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    @elseif($kolom->tipe_input === 'currency')
                        <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-currency w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-mono focus:border-orange-500 outline-none" placeholder="0"><input type="hidden" name="data_tambahan[{{ $kolom->nama_kolom }}]"></div>
                    @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    @elseif($kolom->tipe_input === 'dropdown')
                        <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                            <option value="">Pilih...</option>
                            @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                        </select>
                    @endif
                </div>
            @endforeach
        @endif
    `;

    const htmlKolom2 = `
        @if(isset($kolomTabel2) && $kolomTabel2->count() > 0)
            <div class="md:col-span-2 border-t border-gray-100 pt-3"><h4 class="text-sm font-bold text-gray-800 mb-3">Informasi Tambahan</h4></div>
            @foreach($kolomTabel2 as $kolom)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                    @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    @elseif($kolom->tipe_input === 'currency')
                        <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-currency w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-mono focus:border-orange-500 outline-none" placeholder="0"><input type="hidden" name="data_tambahan[{{ $kolom->nama_kolom }}]"></div>
                    @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    @elseif($kolom->tipe_input === 'dropdown')
                        <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                            <option value="">Pilih...</option>
                            @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                        </select>
                    @endif
                </div>
            @endforeach
        @endif
    `;

    const editHtmlKolom1 = `
        @if(isset($kolomTabel1) && $kolomTabel1->count() > 0)
            @foreach($kolomTabel1 as $kolom)
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                    @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    @elseif($kolom->tipe_input === 'currency')
                        <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 text-xs">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-edit w-full pl-8 pr-3 py-1.5 border border-gray-300 rounded-md text-xs font-mono outline-none focus:border-orange-500" placeholder="0"><input type="hidden" name="data_tambahan[{{ $kolom->nama_kolom }}]"></div>
                    @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    @elseif($kolom->tipe_input === 'dropdown')
                        <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                            <option value="">Pilih...</option>
                            @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                        </select>
                    @endif
                </div>
            @endforeach
        @endif
    `;

    const editHtmlKolom2 = `
        @if(isset($kolomTabel2) && $kolomTabel2->count() > 0)
            @foreach($kolomTabel2 as $kolom)
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                    @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    @elseif($kolom->tipe_input === 'currency')
                        <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 text-xs">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-edit w-full pl-8 pr-3 py-1.5 border border-gray-300 rounded-md text-xs font-mono outline-none focus:border-orange-500" placeholder="0"><input type="hidden" name="data_tambahan[{{ $kolom->nama_kolom }}]"></div>
                    @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    @elseif($kolom->tipe_input === 'dropdown')
                        <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                            <option value="">Pilih...</option>
                            @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                        </select>
                    @endif
                </div>
            @endforeach
        @endif
    `;

    function openModalTambah(kelompok) {
        document.getElementById('formTambah').reset(); 
        document.getElementById('add_kelompok').value = kelompok;
        
        const select = document.getElementById('add_master_id');
        select.innerHTML = '<option value="" disabled selected>Pilih Jenis...</option>';
        const masters = kelompok === 1 ? master1 : master2;
        masters.forEach(m => { select.innerHTML += `<option value="${m.id}">${m.nama_kegiatan}</option>`; });
        
        const container = document.getElementById('dynamic_add_fields_container');
        container.innerHTML = kelompok === 1 ? htmlKolom1 : htmlKolom2;

        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah').value = currentMonth;
        syncPeriode(currentMonth, 'add_tahun', 'add_bulan');
        
        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500', 'bg-red-50'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));

        openModal('modalTambah');
    }

    function editBulan1(tahun, bulan, itemsJson, tambahanJson) {
        document.getElementById('edit_kelompok').value = 1;
        
        const pickerVal = getMonthPickerValue(tahun, bulan);
        document.getElementById('picker_edit').value = pickerVal;
        document.getElementById('edit_tahun').value = tahun;
        document.getElementById('edit_bulan').value = bulan;
        
        const items = JSON.parse(itemsJson);
        const container = document.getElementById('edit_dynamic_inputs');
        container.innerHTML = '';
        
        master1.forEach(m => {
            const val = items[m.id] !== undefined ? items[m.id] : '';
            container.innerHTML += `
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 truncate" title="${m.nama_kegiatan}">${m.nama_kegiatan} <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${m.id}]" value="${val}" required onfocus="if(this.value === '0') this.value = '';" oninput="this.value = this.value.replace(/^0+(?=\d)/, '');" class="dynamic-req w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                </div>
            `;
        });

        const tambahanContainer = document.getElementById('edit_tambahan_inputs');
        if (editHtmlKolom1.trim() !== '') {
            document.getElementById('edit_tambahan_inputs_container').classList.remove('hidden');
            tambahanContainer.innerHTML = editHtmlKolom1;
        } else {
            document.getElementById('edit_tambahan_inputs_container').classList.add('hidden');
        }

        const tambahan = JSON.parse(tambahanJson || '{}');
        document.querySelectorAll('.input-dinamis-edit').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });

        document.querySelectorAll('.dynamic-req').forEach(field => {
            field.addEventListener('input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
        
        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500', 'bg-red-50'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));

        openModal('modalEditBulan');
    }

    function editBulan2(tahun, bulan, itemsJson, tambahanJson) {
        document.getElementById('edit_kelompok').value = 2;
        
        const pickerVal = getMonthPickerValue(tahun, bulan);
        document.getElementById('picker_edit').value = pickerVal;
        document.getElementById('edit_tahun').value = tahun;
        document.getElementById('edit_bulan').value = bulan;
        
        const items = JSON.parse(itemsJson);
        const container = document.getElementById('edit_dynamic_inputs');
        container.innerHTML = '';
        
        master2.forEach(m => {
            const val = items[m.id] !== undefined ? items[m.id] : '';
            container.innerHTML += `
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 truncate" title="${m.nama_kegiatan}">${m.nama_kegiatan} <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${m.id}]" value="${val}" required onfocus="if(this.value === '0') this.value = '';" oninput="this.value = this.value.replace(/^0+(?=\d)/, '');" class="dynamic-req w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                </div>
            `;
        });

        const tambahanContainer = document.getElementById('edit_tambahan_inputs');
        if (editHtmlKolom2.trim() !== '') {
            document.getElementById('edit_tambahan_inputs_container').classList.remove('hidden');
            tambahanContainer.innerHTML = editHtmlKolom2;
        } else {
            document.getElementById('edit_tambahan_inputs_container').classList.add('hidden');
        }

        const tambahan = JSON.parse(tambahanJson || '{}');
        document.querySelectorAll('.input-dinamis-edit').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });

        document.querySelectorAll('.dynamic-req').forEach(field => {
            field.addEventListener('input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
        
        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500', 'bg-red-50'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));

        openModal('modalEditBulan');
    }

    // ================= BLUEPRINT: AUTO-DOT CURRENCY =================
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

    // ================= Validasi Teks Merah Global =================
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
        
        const requiredFields = form.querySelectorAll('[required]:not(.dynamic-req)');
        requiredFields.forEach(field => {
            const eventType = field.tagName === 'SELECT' ? 'change' : 'input';
            field.addEventListener(eventType, function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
    });

    // ================= BLUEPRINT: AUTO OPEN ERROR MODAL =================
    @if($errors->has('nama_kolom') || $errors->has('tipe_input'))
        document.addEventListener('DOMContentLoaded', function() {
            @if(old('modul') == 'pa_teknik_2')
                openModal('modalAturKolom2');
            @else
                openModal('modalAturKolom1');
            @endif
        });
    @endif
        // ================= IMPORT MODAL =================
        document.addEventListener('DOMContentLoaded', function() {
            // Inject hidden kelompok input ke dalam form import
            let importForm = document.getElementById('form_modalImportExcel');
            if (importForm && !document.getElementById('importKelompokField')) {
                let hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'kelompok';
                hiddenInput.id = 'importKelompokField';
                hiddenInput.value = 'tabel1';
                importForm.appendChild(hiddenInput);
            }
        });
        function openImportModal(tabel) {
            // Set kelompok hidden input agar controller tahu Tabel 1 atau Tabel 2
            let kelompokInput = document.getElementById('importKelompokField');
            if (kelompokInput) kelompokInput.value = tabel || 'tabel1';
            openModal('modalImportExcel');
        }

        // ================= BULK DELETE TABEL 1 =================
        function toggleBulkMode1() {
            let container = document.getElementById("tableContainerBulk1");
            let btn = document.getElementById("btnModeBulk1");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll1();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll1() {
            let selectAll = document.getElementById("selectAllBulk1");
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            if (selectAll.checked) {
                // Set flag delete_all = 1 agar semua data di DB terhapus (bukan cuma yang tampil)
                document.getElementById("deleteAllFlag1").value = "1";
                // Langsung eksekusi hapus semua
                let form = document.getElementById("bulkDeleteForm1");
                let formData = new FormData(form);
                fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                }).then(function(res) {
                    if (res.redirected) { window.location.href = res.url; }
                    else { window.location.reload(); }
                }).catch(function() { form.submit(); });
            } else {
                document.getElementById("deleteAllFlag1").value = "0";
                toggleDeleteBtn1();
            }
        }
        function toggleCheckbox1() {
            let selectAll = document.getElementById("selectAllBulk1");
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn1();
        }
        function toggleDeleteBtn1() {
            let group = document.getElementById("btnGroupBulk1");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-1:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll1() {
            let selectAll = document.getElementById("selectAllBulk1");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn1();
            let container = document.getElementById("tableContainerBulk1");
            if (container && !container.classList.contains("hide-bulk")) {
                toggleBulkMode1();
            }
        }

        // ================= BULK DELETE TABEL 2 =================
        function toggleBulkMode2() {
            let container = document.getElementById("tableContainerBulk2");
            let btn = document.getElementById("btnModeBulk2");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll2();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll2() {
            let selectAll = document.getElementById("selectAllBulk2");
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            if (selectAll.checked) {
                document.getElementById("deleteAllFlag2").value = "1";
                let form = document.getElementById("bulkDeleteForm2");
                let formData = new FormData(form);
                fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                }).then(function(res) {
                    if (res.redirected) { window.location.href = res.url; }
                    else { window.location.reload(); }
                }).catch(function() { form.submit(); });
            } else {
                document.getElementById("deleteAllFlag2").value = "0";
                toggleDeleteBtn2();
            }
        }
        function toggleCheckbox2() {
            let selectAll = document.getElementById("selectAllBulk2");
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn2();
        }
        function toggleDeleteBtn2() {
            let group = document.getElementById("btnGroupBulk2");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-2:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
                function submitBulkDelete1() {
            let selectAll = document.getElementById("selectAllBulk1");
            if (selectAll && selectAll.checked) {
                document.getElementById("deleteAllFlag1").value = "1";
            } else {
                document.getElementById("deleteAllFlag1").value = "0";
            }
            let form = document.getElementById("bulkDeleteForm1");
            let formData = new FormData(form);
            // Gather all checked checkboxes in table1 (they're outside the form due to table restrictions)
            document.querySelectorAll(".cb-bulk-1:checked").forEach(function(cb) {
                formData.append("ids[]", cb.value);
            });
            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" }
            }).then(function(res) {
                if (res.redirected) { window.location.href = res.url; }
                else { window.location.reload(); }
            }).catch(function() { form.submit(); });
        }
        function submitBulkDelete2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll && selectAll.checked) {
                document.getElementById("deleteAllFlag2").value = "1";
            } else {
                document.getElementById("deleteAllFlag2").value = "0";
            }
            let form = document.getElementById("bulkDeleteForm2");
            let formData = new FormData(form);
            document.querySelectorAll(".cb-bulk-2:checked").forEach(function(cb) {
                formData.append("ids[]", cb.value);
            });
            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" }
            }).then(function(res) {
                if (res.redirected) { window.location.href = res.url; }
                else { window.location.reload(); }
            }).catch(function() { form.submit(); });
        }
        function cancelAll2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn2();
            let container = document.getElementById("tableContainerBulk2");
            if (container && !container.classList.contains("hide-bulk")) {
                toggleBulkMode2();
            }
        }
</script>
@endsection
