@extends('layouts.app')

@section('content')
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
                <span class="text-gray-400">/</span><span class="text-gray-500">Kearsipan</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">PA Non Teknik</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pengelolaan Dokumen Tekstual</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('pa-tekstual.index') }}" method="GET" class="flex items-center gap-3">
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
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-end items-center">
                                    <div class="relative inline-block text-left mr-2">
            <button type="button" onclick="toggleDropdown('dropdownOpsi1')" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-white text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-gray-200">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Opsi Lanjutan
            </button>
            <div id="dropdownOpsi1" class="hidden absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden origin-top-right transition-all">
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-100"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Aksi & Laporan</p></div>
                <div class="py-1">
                    <button type="button" onclick="openModal('modalImport1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import dari Excel
                    </button>
                    <a href="{{ route('pa-tekstual.export.excel', ['kelompok_tabel' => 1] + request()->query()) }}" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('pa-tekstual.export.pdf', ['kelompok_tabel' => 1] + request()->query()) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export Laporan PDF
                    </a>
                </div>
                @if(auth()->check() && auth()->user()->isAdmin())
                <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi</p></div>
                <div class="py-1">
                    <button type="button" onclick="openModal('modalMaster1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Atur Dokumen
                    </button>
                    <button type="button" onclick="openModal('modalAturKolom1'); toggleDropdown('dropdownOpsi1')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium border-t border-gray-50">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Tambahan
                    </button>
                </div>
                @endif
            </div>
        </div>
                    <button type="button" id="btnModeBulk1" onclick="toggleBulkMode1()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Semua
                </button>
                <x-button variant="primary" onclick="openModalTambah(1)" class="!py-1.5 !px-3 text-xs">Tambah Data</x-button>
        </div>
                <form id="bulkDeleteForm1" action="{{ route('pa-tekstual.destroyBulk') }}" method="POST">
                <input type="hidden" name="delete_all" id="deleteAll1" value="0">
                <input type="hidden" name="filter_tahun" value="{{ request('tahun', 'semua') }}">
                <input type="hidden" name="filter_bulan" value="{{ request('bulan', 'semua') }}">
                <input type="hidden" name="kelompok_tabel" value="1">
            @csrf
            @method('DELETE')
            <div id="btnGroupBulk1" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll1()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>
            <div id="tableContainerBulk1" class="hide-bulk overflow-x-auto">
            @php
                $headTabel1 = ['<input type="checkbox" id="selectAllBulk1" class="bulk-cb-header-1 hidden" onclick="toggleSelectAll1()">', 'Tahun', 'Bulan'];
                foreach($masterTabel1 as $master) { $headTabel1[] = $master->nama_dokumen; }
                foreach($kolomTabel1 as $kolom) { $headTabel1[] = $kolom->nama_kolom; }
                $headTabel1[] = 'Aksi';
            @endphp
            <x-table :headers="$headTabel1">
                @forelse($paginatedTable1 as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk-1 hidden" value="1|{{ $row['tahun'] }}|{{ $row['bulan'] }}" onclick="toggleCheckbox1()"></td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
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
                            <div class="flex gap-2 justify-center">
                                @php $itemsJson = json_encode($row['items']); $tambahanJson = json_encode($row['data_tambahan'] ?? []); @endphp
                                <button type="button" onclick="editBulan(1, '{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}', '{{ $tambahanJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('pa-tekstual.destroyBulan', ['kelompok_tabel' => 1, 'tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headTabel1) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
                
                {{-- GRAND TOTAL ROW --}}
                @if(count($dataTable1) > 0)
                <tr class="bg-gray-100 font-bold text-xs whitespace-nowrap border-t-2 border-gray-300">
                    <td colspan="3" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>
                    @foreach($masterTabel1 as $master)
                        <td class="px-4 py-4 text-orange-700 text-center">{{ number_format($totalsTabel1[$master->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    <td class="px-4 py-4"></td>
                </tr>
                @endif
            </x-table>
            </div>
        </form>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $paginatedTable1->links('pagination::tailwind') }}
        </div>
    </x-card>

    <!-- ================= TABEL 2 ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-end items-center">
                                    <div class="relative inline-block text-left mr-2">
            <button type="button" onclick="toggleDropdown('dropdownOpsi2')" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-white text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-gray-200">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Opsi Lanjutan
            </button>
            <div id="dropdownOpsi2" class="hidden absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden origin-top-right transition-all">
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-100"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Aksi & Laporan</p></div>
                <div class="py-1">
                    <button type="button" onclick="openModal('modalImport2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import dari Excel
                    </button>
                    <a href="{{ route('pa-tekstual.export.excel', ['kelompok_tabel' => 2] + request()->query()) }}" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('pa-tekstual.export.pdf', ['kelompok_tabel' => 2] + request()->query()) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export Laporan PDF
                    </a>
                </div>
                @if(auth()->check() && auth()->user()->isAdmin())
                <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi</p></div>
                <div class="py-1">
                    <button type="button" onclick="openModal('modalMaster2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Atur Dokumen
                    </button>
                    <button type="button" onclick="openModal('modalAturKolom2'); toggleDropdown('dropdownOpsi2')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium border-t border-gray-50">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Tambahan
                    </button>
                </div>
                @endif
            </div>
        </div>
                    <button type="button" id="btnModeBulk2" onclick="toggleBulkMode2()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Semua
                </button>
                <x-button variant="primary" onclick="openModalTambah(2)" class="!py-1.5 !px-3 text-xs">Tambah Data</x-button>
        </div>
                <form id="bulkDeleteForm2" action="{{ route('pa-tekstual.destroyBulk') }}" method="POST">
                <input type="hidden" name="delete_all" id="deleteAll2" value="0">
                <input type="hidden" name="filter_tahun" value="{{ request('tahun', 'semua') }}">
                <input type="hidden" name="filter_bulan" value="{{ request('bulan', 'semua') }}">
                <input type="hidden" name="kelompok_tabel" value="2">
            @csrf
            @method('DELETE')
            <div id="btnGroupBulk2" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll2()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>
            <div id="tableContainerBulk2" class="hide-bulk overflow-x-auto">
            @php
                $headTabel2 = ['<input type="checkbox" id="selectAllBulk2" class="bulk-cb-header-2 hidden" onclick="toggleSelectAll2()">', 'Tahun', 'Bulan'];
                foreach($masterTabel2 as $master) { $headTabel2[] = $master->nama_dokumen; }
                foreach($kolomTabel2 as $kolom) { $headTabel2[] = $kolom->nama_kolom; }
                $headTabel2[] = 'Aksi';
            @endphp
            <x-table :headers="$headTabel2">
                @forelse($paginatedTable2 as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk-2 hidden" value="2|{{ $row['tahun'] }}|{{ $row['bulan'] }}" onclick="toggleCheckbox2()"></td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
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
                            <div class="flex gap-2 justify-center">
                                @php $itemsJson = json_encode($row['items']); $tambahanJson = json_encode($row['data_tambahan'] ?? []); @endphp
                                <button type="button" onclick="editBulan(2, '{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}', '{{ $tambahanJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('pa-tekstual.destroyBulan', ['kelompok_tabel' => 2, 'tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headTabel2) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
                
                {{-- GRAND TOTAL ROW --}}
                @if(count($dataTable2) > 0)
                <tr class="bg-gray-100 font-bold text-xs whitespace-nowrap border-t-2 border-gray-300">
                    <td colspan="3" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>
                    @foreach($masterTabel2 as $master)
                        <td class="px-4 py-4 text-orange-700 text-center">{{ number_format($totalsTabel2[$master->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    <td class="px-4 py-4"></td>
                </tr>
                @endif
            </x-table>
            </div>
        </form>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $paginatedTable2->links('pagination::tailwind') }}
        </div>
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data" message="Data di bulan ini akan dihapus secara permanen. Lanjutkan?" />

    <!-- MODAL TAMBAH DINAMIS -->
    <x-modal id="modalTambah" title="Tambah Data Dokumen" description="Pilih jenis dokumen dan masukkan jumlahnya.">
        <form action="{{ route('pa-tekstual.storeDokumen') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="kelompok_tabel" id="add_kelompok">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_tambah" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'add_tahun', 'add_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="add_tahun">
                <input type="hidden" name="bulan" id="add_bulan">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kegiatan/Dokumen <span class="text-red-500">*</span></label>
                <select name="master_id" id="add_master_id" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" >
                    <option value="" disabled selected>Pilih Jenis...</option>
                    
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bidang ini wajib dipilih!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" required placeholder="Contoh: 5" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Jumlah wajib diisi!</span>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambah')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL EDIT BULAN -->
    <x-modal id="modalEditBulan" title="Edit Data Bulan" description="Perbarui seluruh data pada bulan terkait.">
        <form action="{{ route('pa-tekstual.updateBulan') }}" method="POST" class="grid grid-cols-2 gap-x-4 gap-y-4 novalidate-form" novalidate>
            @csrf
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_edit" required class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500" onchange="syncPeriode(this.value, 'edit_tahun', 'edit_bulan')">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                <input type="hidden" name="tahun" id="edit_tahun">
                <input type="hidden" name="bulan" id="edit_bulan">
            </div>
            <div class="col-span-2 border-b border-gray-100 my-1"></div>
            <div id="edit_dynamic_inputs" class="col-span-2 grid grid-cols-2 gap-4"></div>
            <div class="col-span-2 flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEditBulan')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>
    <!-- MODAL ATUR KOLOM TABEL 1 -->
    <x-modal id="modalMaster1" title="Atur Kolom Tabel 1" description="Daftar kolom/dokumen pada Tabel 1.">
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
            @forelse($masterTabel1 as $m)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-sm font-semibold text-gray-800">{{ $m->nama_dokumen }}</span>
                    <form action="{{ route('pa-tekstual.destroyMaster', $m->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded" onclick="return confirm('Hapus kolom ini dan semua datanya?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada daftar kegiatan. Tambahkan di bawah.</p>
            @endforelse
        </div>
        <form action="{{ route('pa-tekstual.storeMaster') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="1">
            <input type="text" name="nama_dokumen" required placeholder="Nama Kolom Baru..." class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
            <x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Tambah</x-button>
        </form>
    </x-modal>

    <!-- MODAL ATUR KOLOM TABEL 2 -->
    <x-modal id="modalMaster2" title="Atur Kolom Tabel 2" description="Daftar kolom/dokumen pada Tabel 2.">
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
            @forelse($masterTabel2 as $m)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-sm font-semibold text-gray-800">{{ $m->nama_dokumen }}</span>
                    <form action="{{ route('pa-tekstual.destroyMaster', $m->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded" onclick="return confirm('Hapus kolom ini dan semua datanya?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic">Belum ada daftar kegiatan. Tambahkan di bawah.</p>
            @endforelse
        </div>
        <form action="{{ route('pa-tekstual.storeMaster') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="2">
            <input type="text" name="nama_dokumen" required placeholder="Nama Kolom Baru..." class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
            <x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Tambah</x-button>
        </form>
    </x-modal>

    <!-- MODAL ATUR KOLOM TAMBAHAN TABEL 1 -->
    <x-modal id="modalAturKolom1" title="Atur Kolom Tambahan (Tabel 1)" description="Kelola kolom ekstra di luar master dokumen.">
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
            @forelse($kolomTabel1 as $k)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <div>
                        <span class="block text-sm font-semibold text-gray-800">{{ $k->nama_kolom }}</span>
                        <span class="block text-[10px] text-gray-500 uppercase">{{ $k->tipe_input }}</span>
                    </div>
                    <form action="{{ route('pa-tekstual.destroyKolom', $k->id) }}" method="POST">
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
            @forelse($kolomTabel2 as $k)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                    <div>
                        <span class="block text-sm font-semibold text-gray-800">{{ $k->nama_kolom }}</span>
                        <span class="block text-[10px] text-gray-500 uppercase">{{ $k->tipe_input }}</span>
                    </div>
                    <form action="{{ route('pa-tekstual.destroyKolom', $k->id) }}" method="POST">
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

    <!-- MODAL IMPORT TABEL 1 -->
    <x-modal id="modalImport1" title="Import Data Tabel 1" description="Upload file Excel untuk menambahkan data massal ke Tabel 1.">
        <form action="{{ route('pa-tekstual.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="1">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors">
                <input type="file" name="file_excel" id="file_excel1" class="hidden" accept=".xlsx, .xls" required onchange="document.getElementById('fileName1').textContent = this.files[0].name">
                <label for="file_excel1" class="cursor-pointer flex flex-col items-center">
                    <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span class="text-sm font-medium text-gray-600">Klik untuk memilih file Excel</span>
                    <span id="fileName1" class="text-xs text-gray-400 mt-1">Format didukung: .xlsx, .xls</span>
                </label>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="{{ route('pa-tekstual.export.excel', ['kelompok_tabel' => 1, 'template' => 1]) }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Template
                </a>
                <div class="flex gap-2">
                    <x-button variant="outline" type="button" onclick="closeModal('modalImport1')" class="!py-1.5 text-xs">Batal</x-button>
                    <x-button variant="primary" type="submit" class="!py-1.5 text-xs bg-emerald-600 hover:bg-emerald-700 border-none">Import Data</x-button>
                </div>
            </div>
        </form>
    </x-modal>

    <!-- MODAL IMPORT TABEL 2 -->
    <x-modal id="modalImport2" title="Import Data Tabel 2" description="Upload file Excel untuk menambahkan data massal ke Tabel 2.">
        <form action="{{ route('pa-tekstual.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="kelompok_tabel" value="2">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors">
                <input type="file" name="file_excel" id="file_excel2" class="hidden" accept=".xlsx, .xls" required onchange="document.getElementById('fileName2').textContent = this.files[0].name">
                <label for="file_excel2" class="cursor-pointer flex flex-col items-center">
                    <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span class="text-sm font-medium text-gray-600">Klik untuk memilih file Excel</span>
                    <span id="fileName2" class="text-xs text-gray-400 mt-1">Format didukung: .xlsx, .xls</span>
                </label>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="{{ route('pa-tekstual.export.excel', ['kelompok_tabel' => 2, 'template' => 1]) }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Template
                </a>
                <div class="flex gap-2">
                    <x-button variant="outline" type="button" onclick="closeModal('modalImport2')" class="!py-1.5 text-xs">Batal</x-button>
                    <x-button variant="primary" type="submit" class="!py-1.5 text-xs bg-emerald-600 hover:bg-emerald-700 border-none">Import Data</x-button>
                </div>
            </div>
        </form>
    </x-modal>

</main>

<script>
        function toggleDropdown(id) {
        let dropdown = document.getElementById(id);
        if (dropdown.classList.contains('hidden')) {
            // Tutup semua dropdown lain dulu
            document.querySelectorAll('[id^="dropdownOpsi"]').forEach(el => el.classList.add('hidden'));
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }
    
    // Close dropdown on click outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdownOpsi"]').forEach(el => el.classList.add('hidden'));
        }
    });

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

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



    function openModalTambah(kelompok) {
        document.getElementById('add_kelompok').value = kelompok;
        const select = document.getElementById('add_master_id');
        select.innerHTML = '<option value="" disabled selected>Pilih Jenis...</option>';
        const masters = kelompok === 1 ? master1 : master2;
        masters.forEach(m => { select.innerHTML += `<option value="${m.id}">${m.nama_dokumen}</option>`; });
        
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah').value = currentMonth;
        syncPeriode(currentMonth, 'add_tahun', 'add_bulan');
        
        openModal('modalTambah');
    }

    function editBulan(kelompok, tahun, bulan, itemsJson) {
        const pickerVal = getMonthPickerValue(tahun, bulan);
        document.getElementById('picker_edit').value = pickerVal;
        syncPeriode(pickerVal, 'edit_tahun', 'edit_bulan');
        
        const items = JSON.parse(itemsJson);
        const container = document.getElementById('edit_dynamic_inputs');
        container.innerHTML = '';
        
        const masters = kelompok === 1 ? master1 : master2;
        masters.forEach(m => {
            const val = items[m.id] || 0;
            container.innerHTML += `
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 truncate" title="${m.nama_dokumen}">${m.nama_dokumen}</label>
                    <input type="number" name="items[${m.id}]" value="${val}" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                </div>
            `;
        });
        
        openModal('modalEditBulan');
    }

    // ================= Validasi Teks Merah =================
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

    // ================= BULK DELETE TABEL 1 =================
    function toggleBulkMode1() {
        let container = document.getElementById("tableContainerBulk1");
        let btnGroup = document.getElementById("btnGroupBulk1");
        let isBulk = container.classList.contains("bulk-mode");
        
        if (isBulk) {
            cancelAll1();
            container.classList.remove("bulk-mode");
            btnGroup.classList.add("hidden");
            
            document.querySelectorAll(".bulk-cb-header-1").forEach(el => el.classList.add('hidden'));
            document.querySelectorAll(".cb-bulk-1").forEach(el => el.classList.add('hidden'));
        } else {
            container.classList.add("bulk-mode");
            
            document.querySelectorAll(".bulk-cb-header-1").forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll(".cb-bulk-1").forEach(el => el.classList.remove('hidden'));
        }
    }
    
    function toggleSelectAll1() {
        let selectAll = document.getElementById("selectAllBulk1");
        let checkboxes = document.querySelectorAll(".cb-bulk-1");
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        
        let deleteAllInput = document.getElementById("deleteAll1");
        if (deleteAllInput) {
            deleteAllInput.value = selectAll.checked ? '1' : '0';
        }
        toggleDeleteBtn1();
    }
    
    function toggleCheckbox1() {
        let selectAll = document.getElementById("selectAllBulk1");
        let checkboxes = document.querySelectorAll(".cb-bulk-1");
        selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
        
        let deleteAllInput = document.getElementById("deleteAll1");
        if (deleteAllInput) {
            deleteAllInput.value = '0';
        }
        toggleDeleteBtn1();
    }
    
    function toggleDeleteBtn1() {
        let group = document.getElementById("btnGroupBulk1");
        if (group) {
            let checked = document.querySelectorAll(".cb-bulk-1:checked").length > 0;
            if (checked) group.classList.remove("hidden");
            else group.classList.add("hidden");
        }
    }
    
    function cancelAll1() {
        let selectAll = document.getElementById("selectAllBulk1");
        if (selectAll) selectAll.checked = false;
        let checkboxes = document.querySelectorAll(".cb-bulk-1");
        checkboxes.forEach(cb => cb.checked = false);
        toggleDeleteBtn1();
    }

    // ================= BULK DELETE TABEL 2 =================
    function toggleBulkMode2() {
        let container = document.getElementById("tableContainerBulk2");
        let btnGroup = document.getElementById("btnGroupBulk2");
        let isBulk = container.classList.contains("bulk-mode");
        
        if (isBulk) {
            cancelAll2();
            container.classList.remove("bulk-mode");
            btnGroup.classList.add("hidden");
            
            document.querySelectorAll(".bulk-cb-header-2").forEach(el => el.classList.add('hidden'));
            document.querySelectorAll(".cb-bulk-2").forEach(el => el.classList.add('hidden'));
        } else {
            container.classList.add("bulk-mode");
            
            document.querySelectorAll(".bulk-cb-header-2").forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll(".cb-bulk-2").forEach(el => el.classList.remove('hidden'));
        }
    }
    
    function toggleSelectAll2() {
        let selectAll = document.getElementById("selectAllBulk2");
        let checkboxes = document.querySelectorAll(".cb-bulk-2");
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        
        let deleteAllInput = document.getElementById("deleteAll2");
        if (deleteAllInput) {
            deleteAllInput.value = selectAll.checked ? '1' : '0';
        }
        toggleDeleteBtn2();
    }
    
    function toggleCheckbox2() {
        let selectAll = document.getElementById("selectAllBulk2");
        let checkboxes = document.querySelectorAll(".cb-bulk-2");
        selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
        
        let deleteAllInput = document.getElementById("deleteAll2");
        if (deleteAllInput) {
            deleteAllInput.value = '0';
        }
        toggleDeleteBtn2();
    }
    
    function toggleDeleteBtn2() {
        let group = document.getElementById("btnGroupBulk2");
        if (group) {
            let checked = document.querySelectorAll(".cb-bulk-2:checked").length > 0;
            if (checked) group.classList.remove("hidden");
            else group.classList.add("hidden");
        }
    }
    
    function cancelAll2() {
        let selectAll = document.getElementById("selectAllBulk2");
        if (selectAll) selectAll.checked = false;
        let checkboxes = document.querySelectorAll(".cb-bulk-2");
        checkboxes.forEach(cb => cb.checked = false);
        toggleDeleteBtn2();
    }
</script>
@endsection