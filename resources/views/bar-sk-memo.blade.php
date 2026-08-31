@extends('layouts.app')

@section('title', 'BAR SK Memo')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    <!-- MODAL ERROR KUSTOM -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Peringatan</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors">Mengerti</button>
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
                <span class="text-blue-600 font-medium">BAR SK Memo</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">BAR SK Memo</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('bar-sk-memo.index') }}" method="GET" id="filterForm" class="flex items-center gap-3">
            <select name="tahun" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm outline-none focus:border-blue-500 cursor-pointer">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>

            @php
                $bulanDariDB = \Illuminate\Support\Facades\DB::table('bar_sk_memo')->select('bulan')->distinct()->pluck('bulan')->toArray();
                $semuaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $bulanTampil = count($bulanDariDB) > 0 ? $bulanDariDB : $semuaBulan;
                usort($bulanTampil, function($a, $b) use ($semuaBulan) { return array_search($a, $semuaBulan) <=> array_search($b, $semuaBulan); });
            @endphp

            <select name="bulan" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm outline-none focus:border-blue-500 cursor-pointer">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach($bulanTampil as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART 1: BAR SK MEMO TERBIT ================= -->
    <div class="mb-10">
        <div class="h-96 relative z-10">
            <x-dynamic-chart title="BAR SK MEMO TERBIT" subtitle="Distribusi dokumen terbit per bulan (Semua Tahun)" type="bar" id="chartTerbit" />
        </div>
        <div class="flex flex-wrap items-center justify-center gap-4 mt-3 bg-white py-3 px-4 rounded-xl shadow-sm border border-gray-100 text-xs">
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#BAE6FD]"></span><span class="text-gray-700 font-medium">BAR Monitoring</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#3B82F6]"></span><span class="text-gray-700 font-medium">Memo Direksi</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#22C55E]"></span><span class="text-gray-700 font-medium">SKD Kep. Bersama</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#F97316]"></span><span class="text-gray-700 font-medium">SKD Non Ratifikasi</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#1E3A8A]"></span><span class="text-gray-700 font-medium">SKD Ratifikasi</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#FDE047]"></span><span class="text-gray-700 font-medium">BAR Manajemen</span></div>
        </div>
    </div>

    <!-- ================= CHART 2: BAR SK MEMO PROSES ================= -->
    <div class="mb-10">
        <div class="h-96 relative z-10">
            <x-dynamic-chart title="BAR SK MEMO PROSES" subtitle="Distribusi dokumen proses per bulan (Semua Tahun)" type="bar" id="chartProses" />
        </div>
        <div class="flex flex-wrap items-center justify-center gap-4 mt-3 bg-white py-3 px-4 rounded-xl shadow-sm border border-gray-100 text-xs">
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#BAE6FD]"></span><span class="text-gray-700 font-medium">Proses BAR Monitoring</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#3B82F6]"></span><span class="text-gray-700 font-medium">Proses Memo Direksi</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#22C55E]"></span><span class="text-gray-700 font-medium">Proses SKD Kep. Bersama</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#F97316]"></span><span class="text-gray-700 font-medium">Proses SKD Non Ratifikasi</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#1E3A8A]"></span><span class="text-gray-700 font-medium">Proses SKD Ratifikasi</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#FDE047]"></span><span class="text-gray-700 font-medium">Proses BAR Manajemen</span></div>
        </div>
    </div>

    <div class="my-10 relative">
        <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300"></div></div>
        <div class="relative flex justify-center"><span class="bg-[#F8F9FA] px-4 text-sm font-bold text-gray-500 tracking-wide uppercase">Rincian Data Dokumen</span></div>
    </div>

    <!-- ================= TABEL 1: DOKUMEN TERBIT ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-10 z-20 relative">
        <div class="p-5 border-b border-gray-100 bg-white flex flex-col xl:flex-row xl:items-center justify-between gap-4 rounded-t-xl z-[100] relative">
            <h3 class="font-bold text-gray-900 text-lg uppercase">TABEL BAR SK MEMO TERBIT</h3>
            
            <div class="flex items-center gap-3 self-end xl:self-auto">
                <div class="relative inline-block text-left overflow-visible z-[100]">
                    <button type="button" onclick="toggleDropdown('dropdownOpsiTerbit')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiTerbit" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data Excel</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportTerbit'); toggleDropdown('dropdownOpsiTerbit')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Data Massal
                            </button>
                            <a href="{{ route('bar-sk-memo.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan, 'tipe' => 'terbit']) }}" class="text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export Excel Terbit
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data PDF</p>
                        </div>
                        <div class="py-1" role="none">
                            <a href="{{ route('bar-sk-memo.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan, 'tipe' => 'terbit']) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Export PDF Terbit
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolomTerbit'); toggleDropdown('dropdownOpsiTerbit')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel Terbit
                            </button>
                        </div>
                    </div>
                </div>

                <x-button variant="primary" onclick="openModalTambah('Terbit')" class="!py-2 text-xs flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Data Terbit</x-button>
            </div>
        </div>

        <div class="overflow-x-auto">
            @php
                $headTerbit = ['Tahun', 'Bulan', 'SKD Keputusan Bersama', 'SKD Non Ratifikasi', 'SKD Ratifikasi', 'Memo Direksi', 'BAR Monitoring', 'BAR Manajemen'];
                if(isset($kolomDinamisTerbit)) { foreach($kolomDinamisTerbit as $k) { $headTerbit[] = $k->nama_kolom; } }
                $headTerbit[] = 'Aksi';
            @endphp
            <x-table :headers="$headTerbit">
                @forelse($rawData as $row)
                    @if($row->skd_keputusan_bersama_terbit || $row->skd_non_ratifikasi_terbit || $row->skd_ratifikasi_terbit || $row->memo_direksi_terbit || $row->bar_monitoring_terbit || $row->bar_manajemen_terbit || ($row->data_tambahan ?? null))
                    @php $tambahanTerbit = is_string($row->data_tambahan ?? null) ? json_decode($row->data_tambahan ?? '', true) : ($row->data_tambahan ?? []); @endphp
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row->tahun }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row->bulan }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->skd_keputusan_bersama_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->skd_non_ratifikasi_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->skd_ratifikasi_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->memo_direksi_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->bar_monitoring_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->bar_manajemen_terbit }}</td>
                        
                        @if(isset($kolomDinamisTerbit))
                            @foreach($kolomDinamisTerbit as $kolom)
                                <td class="px-4 py-3 text-center text-gray-600">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahanTerbit[$kolom->nama_kolom]))
                                        Rp {{ $tambahanTerbit[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahanTerbit[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-4 py-3 text-center border-l border-gray-100">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editTerbit({{ json_encode($row) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('bar-sk-memo.destroyTerbit') }}" method="POST" class="inline">
                                    @csrf @method('DELETE') <input type="hidden" name="id" value="{{ $row->id }}">
                                    <button type="submit" onclick="return confirm('Hapus baris terbit di bulan ini?')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="{{ count($headTerbit) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
                
                @if(count($rawData) > 0)
                    <tr class="bg-gray-100 text-gray-800 font-bold text-xs whitespace-nowrap border-t border-gray-200">
                        <td colspan="2" class="px-4 py-3 text-right uppercase border-r border-gray-300">Total Keseluruhan</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['skd_kb'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['skd_nr'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['skd_r'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['memo'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['bar_mon'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['bar_man'] }}</td>
                        @if(isset($kolomDinamisTerbit)) @foreach($kolomDinamisTerbit as $k) <td></td> @endforeach @endif
                        <td></td>
                    </tr>
                @endif
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL 2: DOKUMEN PROSES ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-10 z-10 relative">
        <div class="p-5 border-b border-gray-100 bg-white flex flex-col xl:flex-row xl:items-center justify-between gap-4 rounded-t-xl z-[100] relative">
            <h3 class="font-bold text-gray-900 text-lg uppercase">TABEL BAR SK MEMO PROSES</h3>
            
            <div class="flex items-center gap-3 self-end xl:self-auto">
                <div class="relative inline-block text-left overflow-visible z-[100]">
                    <button type="button" onclick="toggleDropdown('dropdownOpsiProses')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiProses" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data Excel</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportProses'); toggleDropdown('dropdownOpsiProses')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Data Massal
                            </button>
                            <a href="{{ route('bar-sk-memo.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan, 'tipe' => 'proses']) }}" class="text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export Excel Proses
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data PDF</p>
                        </div>
                        <div class="py-1" role="none">
                            <a href="{{ route('bar-sk-memo.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan, 'tipe' => 'proses']) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Export PDF Proses
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolomProses'); toggleDropdown('dropdownOpsiProses')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel Proses
                            </button>
                        </div>
                    </div>
                </div>

                <x-button variant="primary" onclick="openModalTambah('Proses')" class="!py-2 text-xs flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Data Proses</x-button>
            </div>
        </div>

        <div class="overflow-x-auto">
            @php
                $headProses = ['Tahun', 'Bulan', 'Proses SKD Kep. Bersama', 'Proses SKD Non Ratifikasi', 'Proses SKD Ratifikasi', 'Proses Memo Direksi', 'Proses BAR Monitoring', 'Proses BAR Manajemen'];
                if(isset($kolomDinamisProses)) { foreach($kolomDinamisProses as $k) { $headProses[] = $k->nama_kolom; } }
                $headProses[] = 'Aksi';
            @endphp
            <x-table :headers="$headProses">
                @forelse($rawData as $row)
                    @if($row->proses_skd_keputusan_bersama || $row->proses_skd_non_ratifikasi || $row->proses_skd_ratifikasi || $row->proses_memo_direksi || $row->proses_bar_monitoring || $row->proses_bar_manajemen || ($row->data_tambahan_proses ?? null))
                    @php $tambahanProses = is_string($row->data_tambahan_proses ?? null) ? json_decode($row->data_tambahan_proses ?? '', true) : ($row->data_tambahan_proses ?? []); @endphp
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row->tahun }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row->bulan }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->proses_skd_keputusan_bersama }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->proses_skd_non_ratifikasi }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->proses_skd_ratifikasi }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->proses_memo_direksi }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->proses_bar_monitoring }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->proses_bar_manajemen }}</td>
                        
                        @if(isset($kolomDinamisProses))
                            @foreach($kolomDinamisProses as $kolom)
                                <td class="px-4 py-3 text-center text-gray-600">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahanProses[$kolom->nama_kolom]))
                                        Rp {{ $tambahanProses[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahanProses[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-4 py-3 text-center border-l border-gray-100">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editProses({{ json_encode($row) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('bar-sk-memo.destroyProses') }}" method="POST" class="inline">
                                    @csrf @method('DELETE') <input type="hidden" name="id" value="{{ $row->id }}">
                                    <button type="submit" onclick="return confirm('Hapus baris proses di bulan ini?')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="{{ count($headProses) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
                
                @if(count($rawData) > 0)
                    <tr class="bg-gray-100 text-gray-800 font-bold text-xs whitespace-nowrap border-t border-gray-200">
                        <td colspan="2" class="px-4 py-3 text-right uppercase border-r border-gray-300">Total Keseluruhan</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalProses['skd_kb'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalProses['skd_nr'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalProses['skd_r'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalProses['memo'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalProses['bar_mon'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalProses['bar_man'] }}</td>
                        @if(isset($kolomDinamisProses)) @foreach($kolomDinamisProses as $k) <td></td> @endforeach @endif
                        <td></td>
                    </tr>
                @endif
            </x-table>
        </div>
    </x-card>

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
            
            <!-- Injeksi Kolom Dinamis Terbit -->
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
            
            <!-- Injeksi Kolom Dinamis Proses -->
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

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    
    function toggleDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) el.classList.remove('hidden');
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    function triggerDeleteKolom(modalAsal, deleteUrl) {
        closeModal(modalAsal);
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
    }

    function toggleDropdownConfig(selectorId, configAreaId) {
        const selector = document.getElementById(selectorId);
        const configArea = document.getElementById(configAreaId);
        if (selector.value === 'dropdown') {
            configArea.classList.remove('hidden');
            configArea.querySelector('input').setAttribute('required', 'true');
        } else {
            configArea.classList.add('hidden');
            configArea.querySelector('input').removeAttribute('required');
        }
    }

    // JS SIHIR: Hapus angka 0 di depan saat input
    document.addEventListener('input', function(e) {
        if (e.target && e.target.type === 'number') {
            let val = e.target.value;
            if (val.length > 1 && val.startsWith('0')) {
                e.target.value = val.replace(/^0+/, '');
                if (e.target.value === '') e.target.value = '0';
            }
        }
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

    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
        }
    }
    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) { return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`; } return '';
    }

    function openModalTambah(tipe) {
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        if(tipe === 'Terbit') {
            document.getElementById('picker_t_terbit').value = currentMonth; syncPeriode(currentMonth, 'tt_thn', 'tt_bln');
            openModal('modalTambahTerbit');
        } else {
            document.getElementById('picker_t_proses').value = currentMonth; syncPeriode(currentMonth, 'tp_thn', 'tp_bln');
            openModal('modalTambahProses');
        }
    }

    function editTerbit(row) {
        const pickerVal = getMonthPickerValue(row.tahun, row.bulan);
        document.getElementById('picker_t_terbit').value = pickerVal; syncPeriode(pickerVal, 'tt_thn', 'tt_bln');
        document.querySelector('#modalTambahTerbit input[name="skd_kb"]').value = row.skd_keputusan_bersama_terbit;
        document.querySelector('#modalTambahTerbit input[name="skd_nr"]').value = row.skd_non_ratifikasi_terbit;
        document.querySelector('#modalTambahTerbit input[name="skd_r"]').value = row.skd_ratifikasi_terbit;
        document.querySelector('#modalTambahTerbit input[name="memo"]').value = row.memo_direksi_terbit;
        document.querySelector('#modalTambahTerbit input[name="bar_mon"]').value = row.bar_monitoring_terbit;
        document.querySelector('#modalTambahTerbit input[name="bar_man"]').value = row.bar_manajemen_terbit;

        // Auto-fill JSON tambahan terbit
        const tambahan = typeof row.data_tambahan === 'string' ? JSON.parse(row.data_tambahan) : (row.data_tambahan || {});
        document.querySelectorAll('.input-dinamis-terbit').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });

        openModal('modalTambahTerbit');
    }

    function editProses(row) {
        const pickerVal = getMonthPickerValue(row.tahun, row.bulan);
        document.getElementById('picker_t_proses').value = pickerVal; syncPeriode(pickerVal, 'tp_thn', 'tp_bln');
        document.querySelector('#modalTambahProses input[name="skd_kb"]').value = row.proses_skd_keputusan_bersama;
        document.querySelector('#modalTambahProses input[name="skd_nr"]').value = row.proses_skd_non_ratifikasi;
        document.querySelector('#modalTambahProses input[name="skd_r"]').value = row.proses_skd_ratifikasi;
        document.querySelector('#modalTambahProses input[name="memo"]').value = row.proses_memo_direksi;
        document.querySelector('#modalTambahProses input[name="bar_mon"]').value = row.proses_bar_monitoring;
        document.querySelector('#modalTambahProses input[name="bar_man"]').value = row.proses_bar_manajemen;

        // Auto-fill JSON tambahan proses
        const tambahan = typeof row.data_tambahan_proses === 'string' ? JSON.parse(row.data_tambahan_proses) : (row.data_tambahan_proses || {});
        document.querySelectorAll('.input-dinamis-proses').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });

        openModal('modalTambahProses');
    }

    // ================= Helper Init Chart =================
    const colors = {!! json_encode($chartColors) !!};
    window.chartDataMap = {};

    function createChart(canvasId, rawData, keys, labels, type = 'bar') {
        const canvas = document.getElementById('canvas_' + canvasId);
        if (!canvas) return null;
        const ctx = canvas.getContext('2d');
        const isBar = type === 'bar';
        const isCircular = (type === 'pie' || type === 'doughnut');

        let chartData;
        let options = {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        };

        if (isCircular) {
            const totals = labels.map(lbl => rawData.reduce((sum, d) => sum + (d.items[lbl] || 0), 0));
            chartData = {
                labels: labels,
                datasets: [{
                    data: totals,
                    backgroundColor: labels.map((_, i) => colors[i % colors.length]),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            };
        } else {
            options.interaction = { mode: 'index', intersect: false };
            options.scales = {
                y: { stacked: true, beginAtZero: true, grid: { color: '#F3F4F6', drawBorder: false } },
                x: { stacked: true, grid: { display: false, drawBorder: false } }
            };
            chartData = {
                labels: rawData.map(d => d.label),
                datasets: keys.map((key, index) => ({
                    label: labels[index],
                    data: rawData.map(d => d.items[labels[index]] || 0),
                    backgroundColor: colors[index % colors.length],
                    stack: 'Stack 0',
                    borderRadius: 4
                }))
            };
        }

        return new Chart(ctx, { type: type, data: chartData, options: options });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const rawDataTerbit = {!! json_encode($chartDataTerbit) !!};
        const rawDataProses = {!! json_encode($chartDataProses) !!};
        
        const keyTerbit = ['bar_mon', 'memo', 'skd_kb', 'skd_nr', 'skd_r', 'bar_man'];
        const lblTerbit = ['BAR Monitoring', 'Memo Direksi', 'SKD Keputusan Bersama', 'SKD Non Ratifikasi', 'SKD Ratifikasi', 'BAR Manajemen'];
        
        const keyProses = ['p_bar_mon', 'p_memo', 'p_skd_kb', 'p_skd_nr', 'p_skd_r', 'p_bar_man'];
        const lblProses = ['Proses BAR Monitoring', 'Proses Memo Direksi', 'Proses SKD Keputusan Bersama', 'Proses SKD Non Ratifikasi', 'Proses SKD Ratifikasi', 'Proses BAR Manajemen'];

        window.chartDataMap['chartTerbit'] = { raw: rawDataTerbit, keys: keyTerbit, labels: lblTerbit };
        window.chartDataMap['chartProses'] = { raw: rawDataProses, keys: keyProses, labels: lblProses };

        window.chart_chartTerbit = createChart('chartTerbit', rawDataTerbit, keyTerbit, lblTerbit, 'bar');
        window.chart_chartProses = createChart('chartProses', rawDataProses, keyProses, lblProses, 'bar');
    });

    function changeChartType(id, type) {
        try { if (window['chart_' + id]) { window['chart_' + id].destroy(); } } catch (e) {}
        window['chart_' + id] = null;
        const map = window.chartDataMap[id];
        if (map) { window['chart_' + id] = createChart(id, map.raw, map.keys, map.labels, type); }
    }
</script>
@endsection
