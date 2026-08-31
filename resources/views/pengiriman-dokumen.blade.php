@extends('layouts.app')

@section('content')
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
            <h3 class="text-xl font-bold text-gray-900 mb-2">Penambahan Gagal</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none'; openModal('modalAturKolom');" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Kembali & Perbaiki
            </button>
        </div>
    </div>
    @endif

    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Pengiriman Dokumen</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pengiriman Dalam Negeri dan Luar Negeri</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
        </div>
        
        <form action="{{ route('pengiriman-dokumen.index') }}" method="GET" class="flex items-center gap-3">
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="semua" {{ $selectedYear == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <select name="month" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="semua" {{ $selectedMonth == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $monthName)
                    <option value="{{ $monthName }}" {{ $selectedMonth == $monthName ? 'selected' : '' }}>{{ $monthName }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= VISUALISASI 1 ================= -->
    <div class="mb-8">
        <x-dynamic-chart title="Statistik Volume Pengiriman & Mailroom" subtitle="Rincian Pengiriman Dokumen pada filter periode laporan." type="bar" id="volumeVolumeChart" />
    </div>

    <!-- ================= TABEL 1 (VOLUME DOKUMEN) ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8 relative">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 rounded-t-xl">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Rincian Volume Dokumen</h3>
                <p class="text-xs text-gray-400">Rincian volume dokumen berdasarkan kategori per bulan</p>
            </div>

            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperPengiriman')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiSuperPengiriman" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportVolume'); toggleActionDropdown('dropdownOpsiSuperPengiriman')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Excel
                            </button>
                            <a href="{{ route('pengiriman-dokumen.export.excel', ['year' => $selectedYear, 'month' => $selectedMonth, 'jenis' => 'volume']) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export Excel
                            </a>
                            <a href="{{ route('pengiriman-dokumen.export.pdf', ['year' => $selectedYear, 'month' => $selectedMonth, 'jenis' => 'volume']) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Export PDF
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolom'); toggleActionDropdown('dropdownOpsiSuperPengiriman')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="openModalTambahVolume()" class="inline-flex justify-center items-center gap-2 rounded-xl border border-transparent px-4 py-2 bg-pkt-jingga text-xs font-medium text-white hover:bg-orange-600 focus:outline-none transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Data Dokumen
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            @php
                $tableHeaders = ['Tahun', 'Bulan', 'Penerimaan mailroom', 'Pengiriman Dalam Negeri', 'Pengiriman Luar Negeri', 'Reg. Surat Masuk DOF'];
                if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $tableHeaders[] = $k->nama_kolom; } }
                $tableHeaders[] = 'Aksi';
            @endphp
            <x-table :headers="$tableHeaders">
                @forelse($costRecords as $record)
                    @php $tambahan = is_string($record->data_tambahan) ? json_decode($record->data_tambahan, true) : ($record->data_tambahan ?? []); @endphp
                    <tr class="hover:bg-gray-50 transition-colors text-sm">
                        <td class="px-6 py-4 text-gray-700 font-medium align-middle text-center">{{ $record->tahun }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium align-middle text-center">{{ $record->bulan }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono align-middle text-center">{{ number_format($record->penerimaan_mailroom, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono align-middle text-center">{{ number_format($record->pengiriman_dalam_negeri, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono align-middle text-center">{{ number_format($record->pengiriman_luar_negeri, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono align-middle text-center">{{ number_format($record->registrasi_surat_masuk_dof, 0, ',', '.') }}</td>
                        
                        <!-- RENDER KOLOM DINAMIS -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-6 py-4 text-gray-600 font-medium align-middle text-center">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                        Rp {{ $tambahan[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick='openEditModalVolume({!! json_encode($record) !!})' class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Edit Laporan Dokumen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusLaporan', '{{ route('pengiriman-dokumen.destroy', $record->id) }}')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Hapus Laporan Keseluruhan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data laporan volume.</td></tr>
                @endforelse
            </x-table>
        </div>
        
        <!-- PAGINATION TABEL 1 -->
        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
            {{ $costRecords->links() }}
        </div>
    </x-card>

    <!-- ================= TABEL 2 (BIAYA ONGKIR) ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8 relative">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 rounded-t-xl">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Rincian Total Ongkir Pengiriman Bulanan</h3>
                <p class="text-xs text-gray-400">Total biaya pengiriman dalam dan luar negeri per bulan</p>
            </div>
            
            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperPengirimanOngkir')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiSuperPengirimanOngkir" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportOngkir'); toggleActionDropdown('dropdownOpsiSuperPengirimanOngkir')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Excel
                            </button>
                            <a href="{{ route('pengiriman-dokumen.export.excel', ['year' => $selectedYear, 'month' => $selectedMonth, 'jenis' => 'ongkir']) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export Excel
                            </a>
                            <a href="{{ route('pengiriman-dokumen.export.pdf', ['year' => $selectedYear, 'month' => $selectedMonth, 'jenis' => 'ongkir']) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Export PDF
                            </a>
                        </div>
                        
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolom'); toggleActionDropdown('dropdownOpsiSuperPengirimanOngkir')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="openModalTambahOngkir()" class="inline-flex justify-center items-center gap-2 rounded-xl border border-transparent px-4 py-2 bg-pkt-jingga text-xs font-medium text-white hover:bg-orange-600 focus:outline-none transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Data Ongkir
                </button>
            </div>
        </div>
        <x-table :headers="['Tahun', 'Bulan', 'Total Ongkir Dalam Negeri', 'Total Ongkir Luar Negeri', 'Aksi']">
            @forelse($costRecords as $cost)
                <tr class="hover:bg-gray-50 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-700 font-medium text-center align-middle">{{ $cost->tahun }}</td>
                    <td class="px-6 py-4 text-gray-900 font-medium text-center align-middle">{{ $cost->bulan }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono font-semibold text-center align-middle">Rp {{ number_format($cost->ongkir_dalam_negeri, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono font-semibold text-center align-middle">Rp {{ number_format($cost->ongkir_luar_negeri, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center align-middle">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" onclick='openEditModalOngkir({!! json_encode($cost) !!})' class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Edit Laporan Ongkir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button type="button" onclick="openDeleteModal('modalHapusLaporan', '{{ route('pengiriman-dokumen.destroy', $cost->id) }}')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Hapus Laporan Keseluruhan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada data biaya ongkir.</td></tr>
            @endforelse
            <tr class="bg-gray-100 border-t border-gray-200 font-bold text-blue-900 text-sm">
                <td colspan="2" class="px-6 py-4 text-right">Total Keseluruhan {{ $selectedMonth == 'semua' ? "Filter" : "$selectedMonth $selectedYear" }} :</td>
                <td class="px-6 py-4 text-pkt-biru font-mono text-center">Rp {{ number_format($totalDomestikOverall, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-pkt-biru font-mono text-center">Rp {{ number_format($totalInternasionalOverall, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </x-table>
        
        <!-- PAGINATION TABEL 2 -->
        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
            {{ $costRecords->links() }}
        </div>
    </x-card>

    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari sistem. Lanjutkan?" />
    <x-delete-modal id="modalHapusLaporan" title="Hapus Data Pengiriman" message="Data pengiriman pada bulan dan tahun ini akan dihapus secara permanen. Lanjutkan?" />

    <!-- MODAL IMPORT PENGIRIMAN DOKUMEN (VOLUME) -->
    <x-import-modal 
        id="modalImportVolume" 
        route="{{ route('pengiriman-dokumen.import', ['jenis' => 'volume']) }}" 
        title="Import Data Dokumen (Volume)" 
        templateRoute="{{ route('pengiriman-dokumen.template-baru', ['jenis' => 'volume']) }}" 
    />

    <!-- MODAL IMPORT PENGIRIMAN ONGKIR -->
    <x-import-modal 
        id="modalImportOngkir" 
        route="{{ route('pengiriman-dokumen.import', ['jenis' => 'ongkir']) }}" 
        title="Import Data Biaya Ongkir" 
        templateRoute="{{ route('pengiriman-dokumen.template-baru', ['jenis' => 'ongkir']) }}" 
    />

    <!-- ================= MODAL ATUR KOLOM ================= -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan" description="Kelola kolom ekstra khusus untuk formulir Pengiriman.">
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
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="pengiriman_dokumen">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option><option value="number">Angka Kuantitas</option><option value="currency">Harga / Uang (Rp)</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH LAPORAN (KHUSUS VOLUME DOKUMEN) ================= -->
    <x-modal id="modalTambahVolume" title="Formulir Laporan Dokumen (Volume)" description="Masukkan data jumlah dokumen untuk periode ini.">
        <form action="{{ route('pengiriman-dokumen.store') }}" method="POST" id="formVolume" class="novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="jenis_form" value="volume">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Bulan & Tahun) <span class="text-red-500">*</span></label>
                    <input type="month" id="periode_input_volume" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none cursor-pointer">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Periode wajib dipilih!</span>
                    <input type="hidden" name="tahun" id="tahun_hidden_volume">
                    <input type="hidden" name="bulan" id="bulan_hidden_volume">
                </div>

                <div class="md:col-span-1"><label class="block text-sm font-medium mb-1.5">Penerimaan Mailroom <span class="text-red-500">*</span></label><input type="number" name="volume_mailroom" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"><span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span></div>
                <div class="md:col-span-1"><label class="block text-sm font-medium mb-1.5">Registrasi Surat Masuk via DOF <span class="text-red-500">*</span></label><input type="number" name="volume_dof" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"><span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span></div>
                <div class="md:col-span-1"><label class="block text-sm font-medium mb-1.5">Pengiriman Dalam Negeri <span class="text-red-500">*</span></label><input type="number" name="volume_domestik" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"><span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span></div>
                <div class="md:col-span-1"><label class="block text-sm font-medium mb-1.5">Pengiriman Luar Negeri <span class="text-red-500">*</span></label><input type="number" name="volume_internasional" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"><span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span></div>

                @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                    <div class="md:col-span-2 border-t pt-5"><h4 class="font-semibold text-sm bg-gray-50 p-3 rounded-lg border">Informasi Tambahan (Kolom Dinamis)</h4></div>
                    @foreach($kolomDinamis as $kolom)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'currency')
                                <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">Rp</span><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-rupiah w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none" placeholder="0"><input type="hidden" name="data_tambahan[{{ $kolom->nama_kolom }}]"></div>
                            @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                                    <option value="">Pilih...</option>
                                    @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                                </select>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahVolume')" class="!px-6 !py-2.5 !rounded-lg text-sm">Batal</x-button>
                <x-button variant="primary" type="submit" class="!px-6 !py-2.5 !rounded-lg text-sm border-none shadow-md">Simpan Dokumen</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH LAPORAN (KHUSUS ONGKIR) ================= -->
    <x-modal id="modalTambahOngkir" title="Formulir Laporan Biaya Ongkir" description="Masukkan data total biaya pengiriman untuk periode ini.">
        <form action="{{ route('pengiriman-dokumen.store') }}" method="POST" id="formOngkir" class="novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="jenis_form" value="ongkir">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Bulan & Tahun) <span class="text-red-500">*</span></label>
                    <input type="month" id="periode_input_ongkir" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none cursor-pointer">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Periode wajib dipilih!</span>
                    <input type="hidden" name="tahun" id="tahun_hidden_ongkir">
                    <input type="hidden" name="bulan" id="bulan_hidden_ongkir">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1.5">Total Ongkir Dalam Negeri <span class="text-red-500">*</span></label>
                    <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span><input type="text" id="input_cost_domestik" required class="input-rupiah w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none"><input type="hidden" name="cost_domestik" required></div>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1.5">Total Ongkir Luar Negeri <span class="text-red-500">*</span></label>
                    <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span><input type="text" id="input_cost_internasional" required class="input-rupiah w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none"><input type="hidden" name="cost_internasional" required></div>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahOngkir')" class="!px-6 !py-2.5 !rounded-lg text-sm">Batal</x-button>
                <x-button variant="primary" type="submit" class="!px-6 !py-2.5 !rounded-lg text-sm border-none shadow-md">Simpan Ongkir</x-button>
            </div>
        </form>
    </x-modal>

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
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

    function toggleActionDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) { el.classList.remove('hidden'); }
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    const bulanIndoList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    
    function setupPeriodeSync(inputId, yearHiddenId, monthHiddenId) {
        const input = document.getElementById(inputId);
        if(!input) return;
        const sync = function() {
            const val = input.value;
            if(val) {
                const parts = val.split('-');
                document.getElementById(yearHiddenId).value = parts[0];
                document.getElementById(monthHiddenId).value = bulanIndoList[parseInt(parts[1], 10) - 1];
                input.classList.remove('border-red-500');
                input.classList.add('border-gray-300');
                if(input.nextElementSibling && input.nextElementSibling.classList.contains('error-msg')) {
                    input.nextElementSibling.classList.add('hidden');
                }
            } else {
                document.getElementById(yearHiddenId).value = '';
                document.getElementById(monthHiddenId).value = '';
            }
        };
        input.addEventListener('change', sync);
        input.addEventListener('input', sync);
        return sync;
    }

    const syncVolume = setupPeriodeSync('periode_input_volume', 'tahun_hidden_volume', 'bulan_hidden_volume');
    const syncOngkir = setupPeriodeSync('periode_input_ongkir', 'tahun_hidden_ongkir', 'bulan_hidden_ongkir');

    function openModalTambahVolume() {
        document.getElementById('formVolume').reset();
        const now = new Date();
        document.getElementById('periode_input_volume').value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        syncVolume();
        document.querySelectorAll('#formVolume .border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('#formVolume .error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambahVolume');
    }

    function openEditModalVolume(record) {
        document.getElementById('formVolume').reset();
        const monthMap = {'Januari':'01', 'Februari':'02', 'Maret':'03', 'April':'04', 'Mei':'05', 'Juni':'06', 'Juli':'07', 'Agustus':'08', 'September':'09', 'Oktober':'10', 'November':'11', 'Desember':'12'};
        document.getElementById('periode_input_volume').value = record.tahun + '-' + monthMap[record.bulan];
        syncVolume();

        document.querySelector('input[name="volume_mailroom"]').value = record.penerimaan_mailroom;
        document.querySelector('input[name="volume_dof"]').value = record.registrasi_surat_masuk_dof;
        document.querySelector('input[name="volume_domestik"]').value = record.pengiriman_dalam_negeri;
        document.querySelector('input[name="volume_internasional"]').value = record.pengiriman_luar_negeri;

        let tambahan = record.data_tambahan;
        if(typeof tambahan === 'string' && tambahan !== '') {
            try { tambahan = JSON.parse(tambahan); } catch(e) { tambahan = {}; }
        }
        if(tambahan && typeof tambahan === 'object') {
            Object.keys(tambahan).forEach(key => {
                const input = document.querySelector(`#formVolume [name="data_tambahan[${key}]"]`);
                if(input) {
                    if(input.classList.contains('input-rupiah')) {
                        const hiddenInput = document.querySelector(`#formVolume input[type="hidden"][name="data_tambahan[${key}]"]`);
                        if(hiddenInput) hiddenInput.value = tambahan[key];
                        input.value = new Intl.NumberFormat('id-ID').format(tambahan[key] || 0);
                    } else {
                        input.value = tambahan[key];
                    }
                }
            });
        }
        document.querySelectorAll('#formVolume .border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('#formVolume .error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambahVolume');
    }

    function openModalTambahOngkir() {
        document.getElementById('formOngkir').reset();
        const now = new Date();
        document.getElementById('periode_input_ongkir').value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        syncOngkir();
        document.querySelectorAll('#formOngkir .border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('#formOngkir .error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambahOngkir');
    }

    function openEditModalOngkir(record) {
        document.getElementById('formOngkir').reset();
        const monthMap = {'Januari':'01', 'Februari':'02', 'Maret':'03', 'April':'04', 'Mei':'05', 'Juni':'06', 'Juli':'07', 'Agustus':'08', 'September':'09', 'Oktober':'10', 'November':'11', 'Desember':'12'};
        document.getElementById('periode_input_ongkir').value = record.tahun + '-' + monthMap[record.bulan];
        syncOngkir();

        document.getElementById('input_cost_domestik').value = new Intl.NumberFormat('id-ID').format(record.ongkir_dalam_negeri);
        document.querySelector('input[name="cost_domestik"]').value = record.ongkir_dalam_negeri;

        document.getElementById('input_cost_internasional').value = new Intl.NumberFormat('id-ID').format(record.ongkir_luar_negeri);
        document.querySelector('input[name="cost_internasional"]').value = record.ongkir_luar_negeri;

        document.querySelectorAll('#formOngkir .border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('#formOngkir .error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambahOngkir');
    }

    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-rupiah')) {
            let rawValue = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?!$)/, '');
            if(e.target.nextElementSibling && e.target.nextElementSibling.tagName === 'INPUT') {
                e.target.nextElementSibling.value = rawValue;
            }
            if (rawValue) { e.target.value = new Intl.NumberFormat('id-ID').format(rawValue); } 
            else { e.target.value = ''; }
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.target && e.target.type === 'number') {
            if (['e', 'E', '+', '-', '.'].includes(e.key)) { e.preventDefault(); }
        }
    });

    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if(form.id === 'formVolume') syncVolume();
            if(form.id === 'formOngkir') syncOngkir();

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

    let volumeVolumeChartInstance = null;
    const volumeChartConfigData = {!! json_encode($chartVolumeConfig ?? []) !!};

    function initVolumeChart(chartType) {
        const volumeChartCanvas = document.getElementById('canvas_volumeVolumeChart');
        if (volumeChartCanvas) {
            const ctx = volumeChartCanvas.getContext('2d');
            if (volumeVolumeChartInstance) volumeVolumeChartInstance.destroy();

            let chartDataToUse = JSON.parse(JSON.stringify(volumeChartConfigData));
            let isEmpty = false;

            if (!chartDataToUse.labels || chartDataToUse.labels.length === 0 || (chartDataToUse.datasets.length > 0 && chartDataToUse.datasets[0].data.length === 0)) {
                isEmpty = true;
            }

            if (isEmpty) {
                chartDataToUse.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                chartDataToUse.datasets.forEach(dataset => { dataset.data = new Array(12).fill(0); });
            }

            const chartOptions = {
                responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                    tooltip: {
                        enabled: !isEmpty, backgroundColor: 'rgba(17, 24, 39, 0.9)', titleFont: { size: 14, weight: 'bold' }, bodyFont: { size: 12 }, padding: 12, cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                return label;
                            }
                        }
                    },
                    beforeDraw: function(chart) {
                        if (isEmpty) {
                            var width = chart.width, height = chart.height, ctx = chart.ctx;
                            ctx.restore();
                            var fontSize = (height / 114).toFixed(2);
                            ctx.font = fontSize + "em sans-serif";
                            ctx.textBaseline = "middle";
                            var text = "Tidak Ada Data Laporan", textX = Math.round((width - ctx.measureText(text).width) / 2), textY = height / 2;
                            ctx.fillStyle = '#9CA3AF';
                            ctx.fillText(text, textX, textY);
                            ctx.save();
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, max: isEmpty ? 10 : undefined, grid: { color: '#F3F4F6' }, ticks: { font: { size: 10, color: '#6B7280' } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: '500', color: '#6B7280' } } }
                }
            };
            volumeVolumeChartInstance = new Chart(ctx, { type: chartType, data: chartDataToUse, options: chartOptions });
        }
    }

    function pengirimanSearch(term) {
        if (!term || term.trim() === '') return;
        term = term.trim();
        const termLower = term.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(function(row) {
            if (row.classList.contains('bg-gray-100')) return;
            row.querySelectorAll('mark.pengiriman-hl').forEach(function(m) {
                const parent = m.parentNode;
                parent.replaceChild(document.createTextNode(m.textContent), m);
                parent.normalize();
            });

            if (row.textContent.toLowerCase().indexOf(termLower) === -1) return;

            row.querySelectorAll('td').forEach(function(td) {
                if (td.querySelector('button')) return;
                const walker = document.createTreeWalker(td, NodeFilter.SHOW_TEXT, null, false);
                const nodes = [];
                let n;
                while ((n = walker.nextNode())) nodes.push(n);

                nodes.forEach(function(textNode) {
                    const content = textNode.nodeValue;
                    if (content.toLowerCase().indexOf(termLower) === -1) return;

                    const escaped = term.replace(/[.*+?^${}()|[\]\\]/g, function(c) { return '\\' + c; });
                    const regex = new RegExp(escaped, 'gi');
                    const frag = document.createDocumentFragment();
                    let last = 0;
                    let m;
                    while ((m = regex.exec(content)) !== null) {
                        if (m.index > last) frag.appendChild(document.createTextNode(content.slice(last, m.index)));
                        const hl = document.createElement('mark');
                        hl.className = 'pengiriman-hl';
                        hl.style.cssText = 'background:yellow;color:#000;font-weight:bold;border-radius:2px;padding:1px 2px;';
                        hl.textContent = m[0];
                        frag.appendChild(hl);
                        last = m.index + m[0].length;
                    }
                    if (last < content.length) frag.appendChild(document.createTextNode(content.slice(last)));
                    textNode.parentNode.replaceChild(frag, textNode);
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        let dynamicType = "{!! $dynamicChartType ?? 'bar' !!}";
        initVolumeChart(dynamicType);
        
        let searchTerm = {!! json_encode(request('search') ?? '') !!};
        if (searchTerm && searchTerm.trim() !== '') {
            setTimeout(function() { pengirimanSearch(searchTerm); }, 100);
        }
    });

    function changeChartType(chartId, newType) {
        if (chartId === 'volumeVolumeChart') initVolumeChart(newType);
    }
</script>
@endsection