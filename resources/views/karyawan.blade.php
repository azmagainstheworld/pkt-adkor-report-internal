@extends('layouts.app')

@section('content')
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <x-success-modal />

    <!-- MODAL ERROR KUSTOM -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Penambahan Gagal</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors">Mengerti</button>
        </div>
    </div>
    @endif

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Karyawan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Karyawan</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
        </div>
    </div>

    <!-- SECTION ATAS: CHART UTAMA -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <x-dynamic-chart id="chartJenis" title="Karyawan Dep. Adkor" subtitle="Berdasarkan jenis karyawan" type="pie">
            <div class="flex items-center justify-around">
                <div class="relative w-32 h-32 rounded-full bg-gray-100 border-[14px] border-blue-200 border-t-pkt-jingga transform -rotate-45">
                    <div class="absolute inset-0 bg-white rounded-full m-1 transform rotate-45 flex flex-col items-center justify-center">
                        <span class="text-xl font-bold text-gray-900">75%</span>
                        <span class="text-[9px] text-gray-400 font-semibold">ORGANIK</span>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-pkt-jingga"></span><span class="text-gray-600">Organik (12)</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-blue-200"></span><span class="text-gray-600">Non Organik (4)</span></div>
                </div>
            </div>
        </x-dynamic-chart>

        <x-dynamic-chart id="chartPensiun" title="Status Ket. Pensiun Karyawan" subtitle="Distribusi berdasarkan masa kerja tersisa" type="pie">
            <div class="flex items-center justify-around">
                <div class="relative w-32 h-32 rounded-full bg-gray-100 border-[14px] border-blue-500 border-t-red-500 border-r-orange-400 transform -rotate-90">
                    <div class="absolute inset-0 bg-white rounded-full m-1 transform rotate-90 flex flex-col items-center justify-center">
                        <span class="text-lg font-bold text-gray-900">> 10 Thn</span>
                        <span class="text-[9px] text-gray-400 font-semibold">DOMINAN</span>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-blue-500"></span><span class="text-gray-600">> 10 Tahun (8)</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-red-500"></span><span class="text-gray-600">< 5 Tahun (4)</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-orange-400"></span><span class="text-gray-600">< 10 Tahun (4)</span></div>
                </div>
            </div>
        </x-dynamic-chart>
    </div>

    <!-- ================= ACTION BAR (TOMBOL-TOMBOL) MINIMALIS ================= -->
    <div class="flex justify-end items-center mb-5">
        
        <!-- Pembungkus Flex Row agar tombol dipaksa sejajar ke samping -->
        <div class="flex flex-row items-center gap-3">
            
            <!-- Opsi Lanjutan (Di sebelah kiri Tambah Karyawan) -->
            <div class="relative inline-block text-left">
                <button type="button" onclick="toggleDropdown('dropdownOpsiSuper')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Opsi Lanjutan
                    <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Isi Dropdown -->
                <div id="dropdownOpsiSuper" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                    
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data EXCEL</p>
                    </div>
                    <div class="py-1" role="none">
                        <button type="button" onclick="openModal('modalImportKaryawan'); toggleDropdown('dropdownOpsiSuper')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import Data Massal
                        </button>
                        <button type="button" onclick="openModal('modalImportKeluarga'); toggleDropdown('dropdownOpsiSuper')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import Data Keluarga
                        </button>
                        <a href="{{ route('karyawan.export.excel', ['type' => 'ringkasan']) }}" class="text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel (Tabel Ringkasan)
                        </a>
                        <a href="{{ route('karyawan.export.excel', ['type' => 'lengkap']) }}" class="text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel (Lengkap + Keluarga)
                        </a>
                    </div>

                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data PDF</p>
                    </div>
                    <div class="py-1" role="none">
                        <a href="{{ route('karyawan.export.pdf', ['type' => 'ringkasan']) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export PDF (Tabel Ringkasan)
                        </a>
                        <a href="{{ route('karyawan.export.pdf', ['type' => 'lengkap']) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export PDF (Lengkap + Keluarga)
                        </a>
                    </div>
                    
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                    </div>
                    <div class="py-1" role="none">
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <button type="button" onclick="openModal('modalAturKolomTabel'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Atur Kolom Tabel
                        </button>
                        <button type="button" onclick="openModal('modalAturKolomProfil'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Atur Kolom Profil
                        </button>
                        @endif
                        
                    </div>
                </div>
            </div>
            
            <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus semua
            </button>

            <!-- Tambah Data Utama (Paling Kanan / Ujung) -->
            <div>
                <button type="button" onclick="bukaModalTambah()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga hover:bg-orange-600 rounded-xl shadow-sm transition-colors border-none outline-none focus:ring-2 focus:ring-orange-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Karyawan
                </button>
            </div>
            
        </div>
    </div>
    <!-- ========================================================================= -->

    <!-- DATA TABLE SECTION -->
        <form id="bulkDeleteForm" action="{{ route('karyawan.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data karyawan terpilih beserta keluarganya?')">
        @csrf
        @method('DELETE')
        
        <div id="btnGroup" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 rounded-t-xl border-b border-red-100">
            <span class="text-xs text-red-600 font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span id="selectedCount">0</span> data terpilih untuk dihapus
            </span>
            <div class="flex gap-2">
                <button type="button" onclick="cancelAll()" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm flex items-center gap-1.5">
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 hide-bulk" id="tableContainerBulk">
        @php
            $headers = ['<input type="checkbox" id="selectAllBulk" onclick="toggleSelectAll()">', 'No', 'Nama', 'NPK', 'Gol/Grade', 'MPP/PBP', 'Ket. Pensiun', 'Keterangan'];
            if(isset($kolomDinamisTabel)) { foreach($kolomDinamisTabel as $k) { $headers[] = $k->nama_kolom; } }
            $headers[] = 'Aksi';
        @endphp

        <x-table :headers="$headers">
            @forelse($karyawan as $index => $item)
                @php $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []); @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 py-2 text-center align-middle">
                        <input type="checkbox" name="ids[]" class="cb-bulk" value="{{ $item->id }}" onclick="toggleCheckbox()">
                    </td>
                    <td class="px-6 py-4 text-center font-medium text-gray-500">
                        {{ $karyawan->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama }}</td>
                    <td class="px-6 py-4 text-gray-500 font-mono text-xs text-center">{{ $item->npk }}</td>
                    <td class="px-6 py-4 text-center"><span class="font-bold text-blue-600">{{ $item->gol_grade }}</span></td>
                    <td class="px-6 py-4 text-center text-gray-700">
                        {{ \Carbon\Carbon::parse($item->mpp_pbp)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $bgPensiun = 'bg-gray-100 text-gray-600';
                            if ($item->ket_pensiun == '> 10 Tahun') {
                                $bgPensiun = 'bg-green-100 text-green-700 border border-green-200';
                            } elseif ($item->ket_pensiun == '< 5 Tahun') {
                                $bgPensiun = 'bg-red-500 text-white font-bold border border-red-600';
                            } elseif ($item->ket_pensiun == '< 10 Tahun') {
                                $bgPensiun = 'bg-orange-100 text-orange-700 border border-orange-200';
                            }
                        @endphp
                        <span class="px-2.5 py-1 rounded-md text-xs font-medium {{ $bgPensiun }}">
                            {{ $item->ket_pensiun ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-md text-xs font-medium {{ $item->keterangan == 'Organik' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }}">
                            {{ $item->keterangan }}
                        </span>
                    </td>
                    
                    
                    <!-- RENDER KOLOM TAMBAHAN TABEL -->
                    @if(isset($kolomDinamisTabel))
                        @foreach($kolomDinamisTabel as $kolom)
                            <td class="px-6 py-4 text-center text-gray-600">
                                @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                    Rp {{ $tambahan[$kolom->nama_kolom] }}
                                @else
                                    {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                    @endif

                    <td class="px-6 py-4">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('karyawan.show', $item->id) }}" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-md border border-blue-200" title="Lihat Detail & Keluarga">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <button type="button" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit"
                                onclick="bukaModalEdit({{ json_encode($item) }})">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button type="button" onclick="openDeleteModal('modalHapusKaryawan', '/karyawan/{{ $item->id }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="15" class="px-6 py-4 text-center text-gray-500 py-6">Tidak ada data karyawan ditemukan.</td></tr>
            @endforelse
        </x-table>
        
        <div class="p-4 border-t border-gray-100 flex justify-end text-sm text-gray-500 bg-white">
            {{ $karyawan->links() }}
        </div>
    </x-card>
    </form>

    <x-delete-modal id="modalHapusKaryawan" title="Hapus Data Karyawan" message="Apakah Anda yakin ingin menghapus data karyawan ini beserta seluruh keluarganya?" />
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari sistem. Lanjutkan?" />

    <!-- MODAL ATUR KOLOM TABEL -->
    <x-modal id="modalAturKolomTabel" title="Atur Kolom (Tabel Karyawan)" description="Kolom ini akan tampil langsung di tabel utama.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
            @if(isset($kolomDinamisTabel) && $kolomDinamisTabel->count() > 0)
                @foreach($kolomDinamisTabel as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('modalAturKolomTabel', '{{ route('karyawan.kolom.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom.</p> @endif
        </div>
        <form action="{{ route('karyawan.kolom.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="karyawan_tabel">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputTabel" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('tipeInputTabel', 'dropdownConfigAreaTabel')">
                        <option value="text">Teks Singkat</option>
                        <option value="number">Angka Kuantitas Biasa</option>
                        <option value="currency">Harga / Uang (Titik Otomatis)</option>
                        <option value="date">Tanggal</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigAreaTabel">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Tinggi, Sedang, Rendah" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolomTabel')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>

    <!-- MODAL ATUR KOLOM PROFIL -->
    <x-modal id="modalAturKolomProfil" title="Atur Kolom (Profil Karyawan)" description="Kolom ini HANYA tampil di halaman detail profil karyawan.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
            @if(isset($kolomDinamisProfil) && $kolomDinamisProfil->count() > 0)
                @foreach($kolomDinamisProfil as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('modalAturKolomProfil', '{{ route('karyawan.kolom.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom.</p> @endif
        </div>
        <form action="{{ route('karyawan.kolom.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="karyawan_profil">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputProfil" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('tipeInputProfil', 'dropdownConfigAreaProfil')">
                        <option value="text">Teks Singkat</option>
                        <option value="number">Angka Kuantitas Biasa</option>
                        <option value="currency">Harga / Uang (Titik Otomatis)</option>
                        <option value="date">Tanggal</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigAreaProfil">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Tinggi, Sedang, Rendah" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolomProfil')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH / EDIT KARYAWAN ================= -->
    <x-modal id="modalKaryawan" title="Formulir Data Karyawan" description="Lengkapi data personal dan atribut karyawan">
        <form action="" method="POST" id="formKaryawan" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="id" id="karyawanId" value="{{ old('id') }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="formNama" value="{{ old('nama') }}" required placeholder="Bambang Setiawan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none transition-colors">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NPK <span class="text-red-500">*</span></label>
                <input type="text" name="npk" id="formNpk" value="{{ old('npk') }}" required placeholder="40xxxxx" class="w-full px-4 py-2.5 border @error('npk') border-red-500 @else border-gray-300 @enderror rounded-lg text-sm outline-none transition-colors">
                @error('npk')
                    <span class="text-red-500 text-[11px] mt-1 font-medium">{{ $message }}</span>
                @else
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Foto</label>
                <input type="url" name="foto" id="formFoto" value="{{ old('foto') }}" placeholder="https://..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" id="formTempatLahir" value="{{ old('tempat_lahir') }}" placeholder="Bontang" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="formTanggalLahir" value="{{ old('tanggal_lahir') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none shadow-inner transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. PTK</label>
                <input type="text" name="no_ptk" id="formNoPtk" value="{{ old('no_ptk') }}" placeholder="00xxxx" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. HP / Telepon</label>
                <input type="text" name="no_hp" id="formNoHp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal MPP/PBP <span class="text-red-500">*</span></label>
                <input type="date" name="mpp_pbp" id="formMppPbp" value="{{ old('mpp_pbp') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none shadow-inner transition-colors">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ket. Pensiun <span class="text-red-500">*</span></label>
                <select name="ket_pensiun" id="formKetPensiun" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 outline-none shadow-sm">
                    <option value="" disabled selected>Pilih...</option>
                    <option value="> 10 Tahun">> 10 Tahun</option>
                    <option value="< 10 Tahun">< 10 Tahun</option>
                    <option value="< 5 Tahun">< 5 Tahun</option>
                </select>
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib dipilih!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Golongan / Grade <span class="text-red-500">*</span></label>
                <select name="gol_grade" id="formGolGrade" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 outline-none">
                    <option value="" disabled selected>Pilih gol...</option>
                    @foreach(['I-A','I-B','I-C','I-D','II-A','II-B','II-C','II-D','III-A','III-B','III-C','III-D','IV-A','IV-B','IV-C','IV-D'] as $gol)
                        <option value="{{ $gol }}">{{ $gol }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib dipilih!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan <span class="text-red-500">*</span></label>
                <select name="keterangan" id="formKeterangan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 outline-none shadow-sm">
                    <option value="" disabled selected>Pilih ket...</option>
                    <option value="Organik">Organik</option>
                    <option value="Non Organik">Non Organik</option>
                </select>
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib dipilih!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="alamat" id="formAlamat" required rows="3" placeholder="Jl. Gladiol No. 1..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none resize-none"></textarea>
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ukuran Kaos <span class="text-red-500">*</span></label>
                <select name="ukuran_kaos" id="formUkuranKaos" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 outline-none shadow-sm">
                    <option value="" disabled selected>Pilih...</option>
                    @foreach(['S','M','L','XL','XXL','XXXL'] as $uk)
                        <option value="{{ $uk }}">{{ $uk }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib dipilih!</span>
            </div>

            <!-- INJEKSI KOLOM DINAMIS (TABEL & PROFIL) DENGAN FULL TIPE INPUT -->
            @if(isset($kolomDinamisTabel))
                @foreach($kolomDinamisTabel as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-karyawan w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-karyawan w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-karyawan w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none" placeholder="0"></div>
                        @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-karyawan w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-karyawan w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                                <option value="">Pilih...</option>
                                @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                            </select>
                        @endif
                    </div>
                @endforeach
            @endif

            @if(isset($kolomDinamisProfil))
                @foreach($kolomDinamisProfil as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }} <span class="text-xs text-blue-500">(Profil)</span></label>
                        @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-karyawan w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-karyawan w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-karyawan w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none" placeholder="0"></div>
                        @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-karyawan w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-karyawan w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                                <option value="">Pilih...</option>
                                @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                            </select>
                        @endif
                    </div>
                @endforeach
            @endif
        </form>

        <x-slot name="footer">
            <x-button variant="outline" onclick="closeModalKaryawan()" class="!px-6 !py-2.5 !rounded-lg">
                Batal
            </x-button>
            <x-button variant="secondary" type="submit" form="formKaryawan" id="btnSubmitKaryawan" class="!px-6 !py-2.5 !rounded-lg !bg-blue-700 hover:!bg-blue-800">
                Simpan Data
            </x-button>
        </x-slot>
    </x-modal>

    <!-- KOMPONEN MODAL IMPORT KARYAWAN -->
    <x-import-modal 
        id="modalImportKaryawan" 
        route="{{ route('karyawan.import') }}" 
        title="Import Data Karyawan" 
        templateRoute="{{ route('template.download', 'karyawan') }}" 
    />

</main>

<script>
    Chart.register(ChartDataLabels);

    const EMPTY_CHART_COLOR = '#E5E7EB';
    const warnaPensiunMap = {
        '> 10 Tahun': '#3B82F6',
        '< 5 Tahun': '#EF4444',
        '< 10 Tahun': '#FB923C',
        'Sudah Pensiun': '#6B7280',
    };

    const labelsPensiunDariDB = {!! json_encode(array_keys($chartPensiunData)) !!};
    const backgroundColorsPensiun = labelsPensiunDariDB.map(label => warnaPensiunMap[label] || '#94A3B8');

    const allChartData = {
        chartJenis: {
            labels: {!! json_encode(array_keys($chartJenisData)) !!},
            data: {!! json_encode(array_values($chartJenisData)) !!},
            colors: ['#F7941E', '#0056A3'],
            hasData: {{ (array_sum($chartJenisData)) > 0 ? 'true' : 'false' }},
        },
        chartPensiun: {
            labels: labelsPensiunDariDB,
            data: {!! json_encode(array_values($chartPensiunData)) !!},
            colors: backgroundColorsPensiun,
            hasData: {{ (array_sum($chartPensiunData)) > 0 ? 'true' : 'false' }},
        },
    };

    const chartInstances = {};

    function buildChartData(config) {
        if (!config.hasData) {
            return {
                labels: ['Tidak ada data'],
                datasets: [{ label: 'Status', data: [1], backgroundColor: [EMPTY_CHART_COLOR], borderWidth: 0 }]
            };
        }
        return {
            labels: config.labels,
            datasets: [{ label: 'Jumlah', data: config.data, backgroundColor: config.colors, borderWidth: 0, hoverOffset: 10 }]
        };
    }

    function renderChart(chartId, type) {
        const canvasEl = document.getElementById('canvas_' + chartId);
        const config = allChartData[chartId];
        if (!canvasEl || !config) return;

        const isEmpty = !config.hasData;
        if (chartInstances[chartId]) chartInstances[chartId].destroy();

        chartInstances[chartId] = new Chart(canvasEl.getContext('2d'), {
            type: type,
            data: buildChartData(config),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: (type === 'bar' && !isEmpty) ? { y: { beginAtZero: true, ticks: { precision: 0 } } } : {},
                plugins: {
                    legend: { display: type !== 'bar', position: 'bottom', labels: { usePointStyle: true, font: { size: 10 }, padding: 15 } },
                    tooltip: {
                        enabled: !isEmpty,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 10,
                        callbacks: {
                            label: (context) => {
                                const val = (context.parsed && typeof context.parsed === 'object') ? (context.parsed.y ?? context.parsed.x) : context.parsed;
                                return ` ${context.label}: ${val} orang`;
                            }
                        }
                    },
                    datalabels: isEmpty ? { display: false } : {
                        color: (type === 'bar') ? '#111827' : '#ffffff',
                        anchor: (type === 'bar') ? 'end' : 'center',
                        align: (type === 'bar') ? 'top' : 'center',
                        font: { weight: 'bold', size: 12 },
                        formatter: (value) => value
                    }
                }
            }
        });
    }

    function changeChartType(chartId, newType) { renderChart(chartId, newType); }

    window.onload = function () {
        renderChart('chartJenis', 'pie');
        renderChart('chartPensiun', 'pie');
    };

    function openModal(id) { const el = document.getElementById(id); if(el) el.classList.remove('hidden'); else console.error('Modal not found:', id); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        let isBulkMode = false;
    function toggleBulkMode() {
        isBulkMode = !isBulkMode;
        const container = document.getElementById('tableContainerBulk');
        const btnGroup = document.getElementById('btnGroup');
        if (container) {
            if (isBulkMode) {
                container.classList.remove('hide-bulk');
                if(btnGroup) btnGroup.classList.remove('hidden');
            } else {
                container.classList.add('hide-bulk');
                if(btnGroup) btnGroup.classList.add('hidden');
                cancelAll();
            }
        }
    }
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAllBulk');
        const checkboxes = document.querySelectorAll('.cb-bulk');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelectedCount();
    }
    function toggleCheckbox() {
        const selectAll = document.getElementById('selectAllBulk');
        const checkboxes = document.querySelectorAll('.cb-bulk');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        selectAll.checked = allChecked;
        updateSelectedCount();
    }
    function updateSelectedCount() {
        const count = document.querySelectorAll('.cb-bulk:checked').length;
        const countEl = document.getElementById('selectedCount');
        if (countEl) countEl.textContent = count;
    }
    function cancelAll() {
        const selectAll = document.getElementById('selectAllBulk');
        if(selectAll) selectAll.checked = false;
        document.querySelectorAll('.cb-bulk').forEach(cb => cb.checked = false);
        updateSelectedCount();
        isBulkMode = false;
        const container = document.getElementById('tableContainerBulk');
        if (container) container.classList.add('hide-bulk');
        const btnGroup = document.getElementById('btnGroup');
        if (btnGroup) btnGroup.classList.add('hidden');
    }

    // --- DROPDOWN TOGGLE LOGIC ---
    function toggleDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        
        // Tutup semua dropdown lain terlebih dahulu
        document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        
        // Buka jika sebelumnya tersembunyi
        if (isHidden) {
            el.classList.remove('hidden');
        }
    }

    // Menutup dropdown jika klik di luar area
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
            configArea.querySelector('input').value = '';
        }
    }

    const modalEl = document.getElementById('modalKaryawan');
    const formKaryawan = document.getElementById('formKaryawan');
    const modalTitle = modalEl.querySelector('h3');
    const methodField = document.getElementById('methodField');

    function bukaModalTambah() {
        modalTitle.textContent = "Tambah Karyawan Baru";
        formKaryawan.action = "{{ route('karyawan.store') }}"; 
        methodField.value = "POST";
        document.getElementById('karyawanId').value = "";
        formKaryawan.reset();

        formKaryawan.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });
        formKaryawan.querySelectorAll('span.error-msg').forEach(el => el.classList.add('hidden'));

        modalEl.classList.remove('hidden');
    }

    function bukaModalEdit(item) {
        modalTitle.textContent = "Edit Data Karyawan";
        formKaryawan.action = "/karyawan/" + item.id;
        methodField.value = "PUT";
        document.getElementById('karyawanId').value = item.id;

        document.getElementById('formNama').value = item.nama;
        document.getElementById('formNpk').value = item.npk;
        document.getElementById('formFoto').value = item.foto || '';
        document.getElementById('formTempatLahir').value = item.tempat_lahir || '';
        document.getElementById('formTanggalLahir').value = item.tanggal_lahir ? item.tanggal_lahir.substring(0, 10) : '';
        document.getElementById('formNoPtk').value = item.no_ptk || '';
        document.getElementById('formMppPbp').value = item.mpp_pbp ? item.mpp_pbp.substring(0, 10) : '';
        document.getElementById('formKetPensiun').value = item.ket_pensiun;
        document.getElementById('formGolGrade').value = item.gol_grade;
        document.getElementById('formKeterangan').value = item.keterangan;
        document.getElementById('formNoHp').value = item.no_hp || '';
        document.getElementById('formAlamat').value = item.alamat;
        document.getElementById('formUkuranKaos').value = item.ukuran_kaos;

        // Auto-fill JSON tambahan
        const tambahan = typeof item.data_tambahan === 'string' ? JSON.parse(item.data_tambahan) : (item.data_tambahan || {});
        document.querySelectorAll('.input-dinamis-karyawan').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });

        formKaryawan.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });
        formKaryawan.querySelectorAll('span.error-msg').forEach(el => el.classList.add('hidden'));

        modalEl.classList.remove('hidden');
    }

    function closeModalKaryawan() { modalEl.classList.add('hidden'); }

    // AUTO-DOT FORMATTER UNTUK HARGA (MATA UANG)
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

    // PENCEGAH HURUF 'e' PADA INPUT NUMBER
    document.addEventListener('keydown', function(e) {
        if (e.target && e.target.type === 'number') {
            if (['e', 'E', '+', '-', '.'].includes(e.key)) { e.preventDefault(); }
        }
    });

    // VALIDASI CLIENT-SIDE CLEAN (Hanya Border Merah & Teks Error di Bawahnya)
    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500');
                    field.classList.remove('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500');
                    field.classList.add('border-gray-300');
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
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                    }
                });
            });
        });
    });
</script>

    
    <!-- Modal Atur Kolom Keluarga -->
    <x-modal id="modalAturKolomKeluarga" title="Atur Kolom Tabel Keluarga">
        <div class="space-y-4">
            <p class="text-sm text-gray-500">
                Tambahkan kolom khusus untuk data keluarga karyawan yang mungkin tidak tersedia secara default.
            </p>
            <form action="{{ route('karyawan.kolom.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="modul" value="keluarga_karyawan">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kolom <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kolom" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="Contoh: No KTP Pasangan">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Input <span class="text-red-500">*</span></label>
                    <select name="tipe_input" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="togglePilihanDropdown(this, 'pilihanKeluarga')">
                        <option value="text">Teks Pendek (String)</option>
                        <option value="number">Angka (Number)</option>
                        <option value="date">Tanggal (Date)</option>
                        <option value="currency">Mata Uang (Rp)</option>
                        <option value="dropdown">Pilihan (Dropdown)</option>
                    </select>
                </div>
                
                <div id="pilihanKeluarga" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan Dropdown <span class="text-red-500">*</span></label>
                    <input type="text" name="pilihan_dropdown" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="Contoh: Kawin, Belum Kawin">
                    <p class="mt-1 text-xs text-gray-500">Pisahkan dengan koma (,)</p>
                </div>
                
                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        Simpan Kolom
                    </button>
                </div>
            </form>
            
            @if(isset($kolomDinamisKeluarga) && $kolomDinamisKeluarga->count() > 0)
                <div class="mt-6 border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Kolom Dinamis Saat Ini:</h4>
                    <div class="space-y-2">
                        @foreach($kolomDinamisKeluarga as $kol)
                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded border">
                                <div>
                                    <span class="text-sm font-medium">{{ $kol->nama_kolom }}</span>
                                    <span class="text-xs text-gray-500 ml-2">({{ $kol->tipe_input }})</span>
                                </div>
                                <form action="{{ route('karyawan.kolom.destroy', $kol->id) }}" method="POST" onsubmit="return confirm('Hapus kolom dinamis ini? Data yang sudah diinput pada kolom ini juga akan hilang.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-modal>

    <!-- Modal Import Data Keluarga -->
    <x-import-modal 
        id="modalImportKeluarga"
        title="Import Data Keluarga Karyawan"
        route="{{ route('karyawan.keluarga.import') }}"
        templateRoute="{{ route('template.download', 'keluarga-karyawan') }}"
    />

    @if($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Buka modal secara otomatis jika ada error validasi
            openModal('modalKaryawan');
            @if(old('id'))
                // Jika sedang mode Edit
                document.getElementById('modalTitle').textContent = 'Edit Data Karyawan';
                document.getElementById('formKaryawan').action = "/karyawan/{{ old('id') }}";
                document.getElementById('methodField').value = "PUT";
            @endif
        });
    </script>
    @endif
@endsection