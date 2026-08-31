@extends('layouts.app')

@section('content')
<!-- Tambahkan Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <!-- Panggil Modal Notifikasi Sukses -->
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
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Mengerti
            </button>
        </div>
    </div>
    @endif

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Anggaran</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Anggaran Administrasi Korporat</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
        </div>

        <!-- Filter Kanan Atas (Tahun & Bulan memfilter SEMUA chart dan tabel di bawah) -->
        <form action="{{ route('anggaran.index') }}" method="GET" class="flex items-center gap-3">
            <!-- Dropdown Tahun -->
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors hover:border-gray-300 hover:bg-gray-50">
                <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}" {{ (string) $selectedYear === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            <!-- Dropdown Bulan -->
            <select name="month" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors hover:border-gray-300 hover:bg-gray-50">
                <option value="all" {{ $selectedMonth === 'all' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach ($bulanOrder as $m)
                    <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @php
        // Label filter yang rapi untuk ditampilkan di teks (handle opsi "Semua")
        $filterLabel = ($selectedMonth === 'all' ? 'Semua Bulan' : $selectedMonth)
            . ' '
            . ($selectedYear === 'all' ? '(Semua Tahun)' : $selectedYear);
    @endphp

    <!-- ================= CHARTS SECTION (3 Kategori: Dikelola, Rutin, Investasi) ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @foreach ($kategoriList as $key => $label)
            <x-dynamic-chart
                id="{{ $key }}Chart"
                title="{{ $label }}"
                subtitle="Penggunaan vs Sisa"
                type="pie"
            ></x-dynamic-chart>
        @endforeach
    </div>

    <!-- Kartu Ringkasan Total Gabungan -->
    <x-card class="!rounded-xl p-6 shadow-sm border border-gray-100 bg-white mb-8">
        <h3 class="font-bold text-gray-900 mb-2">Ringkasan RKAP Keseluruhan</h3>
        <p class="text-xs text-gray-400 mb-5">Total gabungan Anggaran Dikelola + Rutin + Investasi — {{ $filterLabel }}</p>

        <!-- Baris 1: Total RKAP — highlight utama, full width horizontal -->
        <div class="bg-blue-50/60 border border-blue-100 rounded-xl px-6 py-4 mb-4">
            <p class="text-xs text-blue-700 font-semibold mb-1 uppercase tracking-wide">Total RKAP</p>
            <p class="text-lg md:text-xl font-extrabold text-gray-900 font-mono tracking-tight break-words">Rp {{ number_format($totalRkap, 0, ',', '.') }}</p>
        </div>

        <!-- Baris 2: Total Real+Komit & Total Sisa — ditumpuk vertikal -->
        <div class="grid grid-cols-1 gap-4">
            <div class="border border-gray-100 rounded-xl px-6 py-4">
                <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Total Real + Komit</p>
                <p class="text-lg md:text-xl font-extrabold text-gray-900 font-mono tracking-tight break-words">Rp {{ number_format($totalRealPlusKomit, 0, ',', '.') }}</p>
            </div>
            <div class="border border-red-100 bg-red-50/40 rounded-xl px-6 py-4">
                <p class="text-xs text-red-500 font-semibold mb-1 uppercase tracking-wide">Total Sisa</p>
                <p class="text-lg md:text-xl font-extrabold text-red-600 font-mono tracking-tight break-words">Rp {{ number_format($totalSisa, 0, ',', '.') }}</p>
            </div>
        </div>
    </x-card>

    <!-- ================= ACTION BAR MINIMALIS ================= -->
    <div class="flex justify-end items-center mb-5 flex-wrap gap-4">
        
        <!-- Opsi Lanjutan (Di sebelah kiri Tambah) -->
        <div class="relative inline-block text-left">
            <button type="button" onclick="toggleDropdown('dropdownOpsiSuper')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Opsi Lanjutan
                <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <!-- Isi Dropdown -->
            <div id="dropdownOpsiSuper" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                
                <!-- Kategori 1: Kelola Data -->
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p>
                </div>
                <div class="py-1" role="none">
                    <button type="button" onclick="openModal('modalImportAnggaran'); toggleDropdown('dropdownOpsiSuper')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Excel
                    </button>
                    <a href="{{ route('anggaran.export.excel', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('anggaran.export.pdf', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export PDF
                    </a>
                </div>

                <!-- Kategori 2: Atur Kolom -->
                <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                </div>
                <div class="py-1" role="none">
                    <button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Tabel
                    </button>
                </div>
            </div>
        </div>

        <!-- Tambah Data Utama (Paling Kanan / Ujung) -->
        <div>
            <button type="button" onclick="openModalTambah()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga hover:bg-orange-600 rounded-xl shadow-sm transition-colors border-none outline-none focus:ring-2 focus:ring-orange-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Laporan Bulan Ini
            </button>
        </div>

    </div>
    <!-- ========================================================================= -->

    <!-- ================= TABEL 1: RINCIAN PER BULAN, DIKELOMPOKKAN PER KATEGORI ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Rincian Realisasi Penggunaan dan Sisa Anggaran</h3>
            <p class="text-sm text-gray-600">
                Realisasi Penggunaan dan Sisa untuk Anggaran Dikelola, Anggaran Rutin dan Anggaran Investasi yang dikelola
                oleh Departemen Administrasi Korporat — {{ $filterLabel }} sebagai berikut:
            </p>
        </div>

        <div class="overflow-x-auto">
            @php
                $tableHeadersRincian = ['Tahun', 'Bulan', 'Detail', 'Anggaran RKAP', 'Komitmen', 'Realisasi', 'Realisasi + Komitmen', '% Realisasi+Komitmen', 'Sisa Anggaran', '% Sisa Anggaran'];
                if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $tableHeadersRincian[] = $k->nama_kolom; } }
                $tableHeadersRincian[] = 'Aksi';
            @endphp

            <x-table :headers="$tableHeadersRincian">
                @forelse ($detailTable as $entry)
                    @foreach ($entry['kategori'] as $kat => $items)
                        <!-- Baris Header Kategori -->
                        <tr class="bg-blue-50/70">
                            <td class="px-6 py-2.5 text-gray-700 font-semibold text-center align-middle whitespace-nowrap">{{ $entry['tahun'] }}</td>
                            <td class="px-6 py-2.5 text-gray-700 font-semibold text-center align-middle whitespace-nowrap">{{ $entry['bulan'] }}</td>
                            <td class="px-6 py-2.5 font-extrabold text-blue-900 text-left align-middle" colspan="{{ 9 + (isset($kolomDinamis) ? count($kolomDinamis) : 0) }}">{{ $kategoriList[$kat] }}</td>
                        </tr>

                        <!-- Baris Detail -->
                        @foreach ($items as $anggaran)
                            @php
                                $realPlusKomit = $anggaran->komitmen + $anggaran->realisasi;
                                $sisaAnggaran = $anggaran->rkap - $realPlusKomit;
                                $percRk = ($anggaran->rkap > 0) ? ($realPlusKomit / $anggaran->rkap) * 100 : 0;
                                $percSisa = ($anggaran->rkap > 0) ? ($sisaAnggaran / $anggaran->rkap) * 100 : 0;

                                if ($percRk > 80) { $rkColor = "bg-red-900/10 text-red-900"; }
                                elseif ($percRk >= 50) { $rkColor = "bg-orange-100 text-orange-700"; }
                                else { $rkColor = "bg-yellow-100 text-yellow-700"; }

                                $tambahan = is_string($anggaran->data_tambahan) ? json_decode($anggaran->data_tambahan, true) : ($anggaran->data_tambahan ?? []);
                            @endphp
                            <tr class="hover:bg-gray-100 transition-colors text-sm">
                                <td class="px-6 py-3 text-gray-700 text-center align-middle whitespace-nowrap">{{ $entry['tahun'] }}</td>
                                <td class="px-6 py-3 text-gray-600 text-center align-middle whitespace-nowrap">{{ $entry['bulan'] }}</td>
                                <td class="px-6 py-3 font-bold text-gray-900 text-left align-middle">{{ $anggaran->detail_anggaran }}</td>
                                <td class="px-6 py-3 text-gray-600 font-mono text-center align-middle">{{ number_format($anggaran->rkap, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-gray-600 font-mono text-center align-middle">{{ number_format($anggaran->komitmen, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-gray-600 font-mono text-center align-middle">{{ number_format($anggaran->realisasi, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-gray-900 font-extrabold font-mono text-center align-middle">{{ number_format($realPlusKomit, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-center align-middle">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $rkColor }}">{{ round($percRk, 1) }}%</span>
                                </td>
                                <td class="px-6 py-3 text-gray-900 font-extrabold font-mono text-center align-middle {{ $sisaAnggaran < 0 ? 'text-red-600' : '' }}">
                                    {{ number_format($sisaAnggaran, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 text-center align-middle">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700 border border-green-200">{{ round($percSisa, 1) }}%</span>
                                </td>

                                <!-- RENDER KOLOM DINAMIS -->
                                @if(isset($kolomDinamis))
                                    @foreach($kolomDinamis as $kolom)
                                        <td class="px-6 py-3 text-center text-gray-600 font-medium">
                                            @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                                Rp {{ $tambahan[$kolom->nama_kolom] }}
                                            @else
                                                {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif

                                <!-- AKSI -->
                                <td class="px-6 py-3 text-center align-middle">
                                    <div class="flex justify-center gap-2">
                                        <button type="button" 
                                                class="btn-edit-anggaran p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" 
                                                data-id="{{ $anggaran->id }}"
                                                data-tahun="{{ $anggaran->tahun }}"
                                                data-bulan="{{ $anggaran->bulan }}"
                                                data-kategori="{{ $anggaran->kategori }}"
                                                data-detail="{{ $anggaran->detail_anggaran }}"
                                                data-rkap="{{ $anggaran->rkap }}"
                                                data-komitmen="{{ $anggaran->komitmen }}"
                                                data-realisasi="{{ $anggaran->realisasi }}"
                                                data-keterangan="{{ $anggaran->keterangan }}"
                                                data-tambahan="{{ json_encode($anggaran->data_tambahan) }}"
                                                data-url="{{ route('anggaran.update', $anggaran->id) }}"
                                                onclick="openModalEditAnggaran(this.dataset)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button type="button" 
                                                onclick="openDeleteModal('modalHapusAnggaran', '{{ route('anggaran.destroy', $anggaran->id) }}')" 
                                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    
                    <!-- SUBTOTAL ROW PER BULAN/TAHUN -->
                    @php
                        $totRkap = $entry['total_rkap'] ?? 0;
                        $totKomitmen = $entry['total_komitmen'] ?? 0;
                        $totRealisasi = $entry['total_realisasi'] ?? 0;
                        $totRealPlusKomit = $totKomitmen + $totRealisasi;
                        $totSisa = $totRkap - $totRealPlusKomit;
                        $totPercRk = $totRkap > 0 ? ($totRealPlusKomit / $totRkap) * 100 : 0;
                        $totPercSisa = $totRkap > 0 ? ($totSisa / $totRkap) * 100 : 0;
                    @endphp
                    <tr class="bg-yellow-50/50 border-t-2 border-b-2 border-yellow-200 shadow-sm">
                        <td class="px-6 py-4 font-extrabold text-gray-900 text-right align-middle" colspan="3">SUBTOTAL {{ strtoupper($entry['bulan']) }} {{ $entry['tahun'] }}</td>
                        <td class="px-6 py-4 text-gray-900 font-extrabold font-mono text-center align-middle">Rp{{ number_format($totRkap, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-900 font-extrabold font-mono text-center align-middle">Rp{{ number_format($totKomitmen, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-900 font-extrabold font-mono text-center align-middle">Rp{{ number_format($totRealisasi, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-900 font-extrabold font-mono text-center align-middle">Rp{{ number_format($totRealPlusKomit, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-900 font-extrabold text-center align-middle">{{ round($totPercRk, 0) }}%</td>
                        <td class="px-6 py-4 text-gray-900 font-extrabold font-mono text-center align-middle">Rp{{ number_format($totSisa, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-900 font-extrabold text-center align-middle">{{ round($totPercSisa, 0) }}%</td>
                        <td colspan="{{ 1 + (isset($kolomDinamis) ? count($kolomDinamis) : 0) }}" class="bg-gray-50/50"></td>
                    </tr>
                    
                @empty
                    <tr><td colspan="15" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data anggaran untuk filter yang dipilih.</td></tr>
                @endforelse
            </x-table>
        </div>
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white rounded-b-xl">
            <div>@if ($detailTable->total() > 0) Menampilkan {{ $detailTable->firstItem() }}–{{ $detailTable->lastItem() }} dari {{ $detailTable->total() }} bulan @else Tidak ada data ditemukan @endif</div>
            <div>{{ $detailTable->links() }}</div>
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINGKASAN TOTAL GABUNGAN PER BULAN ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Ringkasan Realisasi & Komitmen per Bulan</h3>
            <p class="text-sm text-gray-600">Total gabungan seluruh kategori (Dikelola + Rutin + Investasi) sesuai filter Tahun/Bulan di atas.</p>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['Tahun', 'Bulan', '% Realisasi & Komitmen', '% Sisa Anggaran', 'Anggaran RKAP', 'Komitmen', 'Realisasi', 'Realisasi + Komitmen', 'Sisa Anggaran']">
                @forelse ($summaryPerBulan as $row)
                    <tr class="hover:bg-gray-100 transition-colors text-sm">
                        <td class="px-6 py-3 text-gray-700 text-center align-middle whitespace-nowrap">{{ $row['tahun'] }}</td>
                        <td class="px-6 py-3 text-gray-600 text-center align-middle whitespace-nowrap">{{ $row['bulan'] }}</td>
                        <td class="px-6 py-3 text-center align-middle">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-orange-100 text-orange-700">{{ $row['percRk'] }}%</span>
                        </td>
                        <td class="px-6 py-3 text-center align-middle">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700">{{ $row['percSisa'] }}%</span>
                        </td>
                        <td class="px-6 py-3 text-gray-600 font-mono text-center align-middle">{{ number_format($row['rkap'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-gray-600 font-mono text-center align-middle">{{ number_format($row['komitmen'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-gray-600 font-mono text-center align-middle">{{ number_format($row['realisasi'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-gray-900 font-extrabold font-mono text-center align-middle">{{ number_format($row['realPlusKomit'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-gray-900 font-extrabold font-mono text-center align-middle {{ $row['sisa'] < 0 ? 'text-red-600' : '' }}">{{ number_format($row['sisa'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data untuk filter yang dipilih.</td></tr>
                @endforelse
            </x-table>
        </div>
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white rounded-b-xl">
            <div>@if ($summaryPerBulan->total() > 0) Menampilkan {{ $summaryPerBulan->firstItem() }}–{{ $summaryPerBulan->lastItem() }} dari {{ $summaryPerBulan->total() }} bulan @else Tidak ada data ditemukan @endif</div>
            <div>{{ $summaryPerBulan->links() }}</div>
        </div>
    </x-card>

    <!-- ================= TABEL 3: PERBANDINGAN SISA ANGGARAN ANTAR KATEGORI ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Perbandingan Sisa Anggaran Antar Kategori</h3>
            <p class="text-sm text-gray-600">Sisa anggaran tiap kategori (Dikelola, Rutin, Investasi) sesuai filter Tahun/Bulan di atas.</p>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['Tahun', 'Bulan', 'Keterangan', 'Anggaran Dikelola', 'Anggaran Rutin', 'Anggaran Investasi']">
                @forelse ($sisaPerKategori as $row)
                    <tr class="hover:bg-gray-100 transition-colors text-sm">
                        <td class="px-6 py-3 text-gray-700 text-center align-middle whitespace-nowrap">{{ $row['tahun'] }}</td>
                        <td class="px-6 py-3 text-gray-600 text-center align-middle whitespace-nowrap">{{ $row['bulan'] }}</td>
                        <td class="px-6 py-3 text-gray-600 text-center align-middle">{{ $row['keterangan'] }}</td>
                        <td class="px-6 py-3 font-mono text-center align-middle {{ $row['sisa']['Dikelola'] < 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($row['sisa']['Dikelola'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 font-mono text-center align-middle {{ $row['sisa']['Rutin'] < 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($row['sisa']['Rutin'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 font-mono text-center align-middle {{ $row['sisa']['Investasi'] < 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($row['sisa']['Investasi'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data untuk filter yang dipilih.</td></tr>
                @endforelse
            </x-table>
        </div>
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white rounded-b-xl">
            <div>@if ($sisaPerKategori->total() > 0) Menampilkan {{ $sisaPerKategori->firstItem() }}–{{ $sisaPerKategori->lastItem() }} dari {{ $sisaPerKategori->total() }} bulan @else Tidak ada data ditemukan @endif</div>
            <div>{{ $sisaPerKategori->links() }}</div>
        </div>
    </x-card>

    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari sistem. Lanjutkan?" />
    <x-delete-modal id="modalHapusAnggaran" title="Hapus Data Anggaran" message="Apakah Anda yakin ingin menghapus data anggaran ini? Tindakan ini akan mempengaruhi rekapitulasi realisasi dan sisa anggaran pada bulan terkait." />

    <!-- MODAL ATUR KOLOM -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Anggaran" description="Kelola kolom ekstra khusus untuk formulir Anggaran.">
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
                        <button type="button" onclick="triggerDeleteKolom('modalAturKolom', '{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p> @endif
        </div>

        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="anggaran">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputAnggaran" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('tipeInputAnggaran', 'dropdownConfigAreaAnggaran')">
                        <option value="text">Teks Singkat</option>
                        <option value="number">Angka Kuantitas Biasa</option>
                        <option value="currency">Harga / Uang (Titik Otomatis)</option>
                        <option value="date">Tanggal</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigAreaAnggaran">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Tinggi, Sedang, Rendah" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>

    <!-- MODAL TAMBAH ANGGARAN -->
    <x-modal id="modalTambahAnggaran" title="Tambah Laporan Anggaran Bulanan Korporat" description="Masukkan data mentah anggaran untuk bulan laporan. Kalkulasi persentase dan sisa akan dilakukan otomatis oleh sistem.">
        <form id="formTambahAnggaran" action="{{ route('anggaran.store') }}" method="POST" class="novalidate-form" novalidate>
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                <!-- Field Periode -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Bulan & Tahun) <span class="text-red-500">*</span></label>

                    @php
                        $bulanIndoList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        $oldPeriode = '';
                        if (old('tahun') && old('bulan')) {
                            $idxBulanLama = array_search(old('bulan'), $bulanIndoList);
                            if ($idxBulanLama !== false) {
                                $oldPeriode = old('tahun') . '-' . str_pad($idxBulanLama + 1, 2, '0', STR_PAD_LEFT);
                            }
                        } else {
                            $oldPeriode = now()->format('Y-m');
                        }
                    @endphp

                    <input type="month"
                           id="periode_input"
                           lang="id"
                           required
                           value="{{ $oldPeriode }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner cursor-pointer">

                    <p id="periode_display" class="text-xs text-gray-500 mt-1.5">Terpilih: <span class="font-semibold text-gray-700">-</span></p>

                    <input type="hidden" name="tahun" id="tahun_hidden" value="{{ old('tahun') }}">
                    <input type="hidden" name="bulan" id="bulan_hidden" value="{{ old('bulan') }}">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Periode wajib diisi!</span>
                </div>

                <!-- Field Kategori Anggaran -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori Anggaran <span class="text-red-500">*</span></label>
                    <select name="kategori" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                        <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih Kategori</option>
                        @foreach ($kategoriList as $key => $label)
                            <option value="{{ $key }}" {{ old('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Kategori wajib dipilih!</span>
                </div>

                <!-- Field Detail Anggaran -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama/Detail Pos Anggaran <span class="text-red-500">*</span></label>
                    <input type="text" name="detail_anggaran" required value="{{ old('detail_anggaran') }}" placeholder="Contoh: Cetak dan Fotocopy" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Detail pos wajib diisi!</span>
                </div>

                <!-- Field Anggaran RKAP -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Anggaran RKAP <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="text"
                               id="rkap_input"
                               required
                               value="{{ old('rkap') ? number_format((float) old('rkap'), 0, ',', '.') : '' }}"
                               placeholder="1.000.000"
                               class="input-currency w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 font-mono font-extrabold focus:outline-none focus:border-blue-500 shadow-inner">
                        <input type="hidden" name="rkap" id="rkap_hidden" value="{{ old('rkap') }}">
                    </div>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Anggaran RKAP wajib diisi!</span>
                </div>

                <!-- Field Komitmen & Realisasi -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Komitmen Bulan Ini <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="text"
                               id="komitmen_input"
                               required
                               value="{{ old('komitmen') ? number_format((float) old('komitmen'), 0, ',', '.') : '' }}"
                               placeholder="10.000"
                               class="input-currency w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 font-mono focus:outline-none focus:border-blue-500 shadow-inner">
                        <input type="hidden" name="komitmen" id="komitmen_hidden" value="{{ old('komitmen') }}">
                    </div>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Komitmen wajib diisi!</span>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Realisasi Bulan Ini <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="text"
                               id="realisasi_input"
                               required
                               value="{{ old('realisasi') ? number_format((float) old('realisasi'), 0, ',', '.') : '' }}"
                               placeholder="100.000"
                               class="input-currency w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 font-mono focus:outline-none focus:border-blue-500 shadow-inner">
                        <input type="hidden" name="realisasi" id="realisasi_hidden" value="{{ old('realisasi') }}">
                    </div>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Realisasi wajib diisi!</span>
                </div>

                <!-- Field Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan Tambahan</label>
                    <textarea name="keterangan" rows="3" placeholder="Contoh: Penggunaan Sisa RKAP" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner">{{ old('keterangan') }}</textarea>
                </div>

                <!-- INJEKSI KOLOM DINAMIS ANGGARAN -->
                @if(isset($kolomDinamis))
                    @foreach($kolomDinamis as $kolom)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'currency')
                                <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-currency w-full pl-9 pr-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm outline-none" placeholder="0"></div>
                            @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm outline-none">
                                    <option value="">Pilih...</option>
                                    @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                                </select>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="flex justify-end gap-3 mt-8 border-t border-gray-100 pt-6">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahAnggaran')" class="!rounded-xl !text-xs !py-2.5 !px-5">Batal</x-button>
                <x-button variant="primary" type="submit" class="!rounded-xl !text-xs !py-2.5 !px-5">Simpan Laporan Bulanan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL IMPORT ANGGARAN -->
    <x-import-modal 
        id="modalImportAnggaran" 
        route="{{ route('anggaran.import') }}" 
        title="Import Data Anggaran Bulanan" 
        templateRoute="{{ route('template.download', 'anggaran') }}" 
    />

</main>

{{-- ================= CHARTS JAVASCRIPT CONFIGURATION ================= --}}
<script>
    Chart.register(ChartDataLabels);

    const allChartData = {!! json_encode($chartConfig) !!};
    const chartInstances = {};
    const EMPTY_CHART_COLOR = '#E5E7EB';

    function buildChartData(config) {
        if (!config.hasData) {
            return {
                labels: ['Tidak ada data'],
                datasets: [{
                    label: 'Status',
                    data: [1],
                    backgroundColor: [EMPTY_CHART_COLOR],
                    borderWidth: 0
                }]
            };
        }

        return {
            labels: config.labels,
            datasets: [{
                label: 'Persentase',
                data: config.data,
                backgroundColor: config.colors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        };
    }

    function renderChart(chartId, type) {
        const canvasEl = document.getElementById('canvas_' + chartId);
        const config = allChartData[chartId];
        if (!canvasEl || !config) return;

        const isEmpty = !config.hasData;

        if (chartInstances[chartId]) {
            chartInstances[chartId].destroy();
        }

        chartInstances[chartId] = new Chart(canvasEl.getContext('2d'), {
            type: type,
            data: buildChartData(config),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: (type === 'bar' && !isEmpty) ? {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (v) => v + '%' }
                    }
                } : {},
                plugins: {
                    legend: {
                        display: type !== 'bar',
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            font: { size: 10, family: 'Arial' },
                            padding: 15
                        }
                    },
                    tooltip: {
                        enabled: !isEmpty,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 10,
                        titleFont: { weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: (context) => {
                                const val = (context.parsed && typeof context.parsed === 'object')
                                    ? (context.parsed.y ?? context.parsed.x)
                                    : context.parsed;
                                return ` ${context.label}: ${Number(val).toFixed(1)}%`;
                            }
                        }
                    },
                    datalabels: isEmpty ? { display: false } : {
                        color: '#ffffff',
                        font: { weight: 'extrabold', size: 12 },
                        formatter: (value) => `${Number(value).toFixed(1)}%`
                    }
                }
            }
        });
    }

    function changeChartType(chartId, newType) {
        renderChart(chartId, newType);
    }

    window.onload = function () {
        Object.keys(allChartData).forEach(function (chartId) {
            renderChart(chartId, allChartData[chartId].type);
        });
    };
</script>

{{-- ================= JAVASCRIPT FOR FORM & MONEY INPUT ================= --}}
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

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

    function openModalTambah() {
        const form = document.getElementById('formTambahAnggaran');
        form.reset();
        form.action = "{{ route('anggaran.store') }}";
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        
        document.getElementById('modalTambahAnggaran').querySelector('h3').innerText = 'Tambah Laporan Anggaran Bulanan Korporat';
        form.querySelector('button[type="submit"]').innerHTML = '<svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Simpan Data';
        
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-200');
        });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambahAnggaran');
    }

    function openModalEditAnggaran(data) {
        const form = document.getElementById('formTambahAnggaran');
        form.reset();
        form.action = data.url;
        
        if (!form.querySelector('input[name="_method"]')) {
            form.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
        }

        document.getElementById('modalTambahAnggaran').querySelector('h3').innerText = 'Edit Laporan Anggaran Bulanan Korporat';
        form.querySelector('button[type="submit"]').innerHTML = '<svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Simpan Perubahan';

        const bulanIndoList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const mIdx = bulanIndoList.indexOf(data.bulan) + 1;
        const monthNumStr = mIdx < 10 ? '0' + mIdx : mIdx;
        
        document.getElementById('periode_input').value = data.tahun + '-' + monthNumStr;
        document.getElementById('kategori').value = data.kategori;
        document.getElementById('detail_anggaran').value = data.detail;
        document.getElementById('rkap').value = new Intl.NumberFormat('id-ID').format(data.rkap);
        document.getElementById('komitmen').value = new Intl.NumberFormat('id-ID').format(data.komitmen);
        document.getElementById('realisasi').value = new Intl.NumberFormat('id-ID').format(data.realisasi);
        document.getElementById('keterangan').value = data.keterangan;

        if (data.tambahan) {
            try {
                const tambahanData = JSON.parse(data.tambahan);
                for (const [key, val] of Object.entries(tambahanData)) {
                    const inputEl = form.querySelector(`[name="data_tambahan[${key}]"]`);
                    if (inputEl) inputEl.value = val;
                }
            } catch (e) {}
        }

        syncPeriode();

        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-200');
        });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambahAnggaran');
    }

    // SINKRONISASI PERIODE
    const bulanIndoList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const periodeInput = document.getElementById('periode_input');
    const tahunHidden = document.getElementById('tahun_hidden');
    const bulanHidden = document.getElementById('bulan_hidden');
    const periodeDisplay = document.getElementById('periode_display');

    function syncPeriode() {
        const val = periodeInput.value;
        if (!val) {
            tahunHidden.value = '';
            bulanHidden.value = '';
            periodeDisplay.innerHTML = 'Terpilih: <span class="font-semibold text-gray-700">-</span>';
            return;
        }

        const [tahun, bulanAngka] = val.split('-');
        const namaBulan = bulanIndoList[parseInt(bulanAngka, 10) - 1] || '-';

        tahunHidden.value = tahun;
        bulanHidden.value = namaBulan;
        periodeDisplay.innerHTML = `Terpilih: <span class="font-semibold text-gray-700">${namaBulan} ${tahun}</span>`;
    }

    periodeInput.addEventListener('change', syncPeriode);
    periodeInput.addEventListener('input', syncPeriode);
    syncPeriode();

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

    // VALIDASI CLIENT-SIDE CLEAN (Hanya border merah & teks error di bawah)
    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Bersihkan format titik untuk hidden field angka
            ['rkap', 'komitmen', 'realisasi'].forEach(id => {
                const vis = document.getElementById(id + '_input');
                const hid = document.getElementById(id + '_hidden');
                if(vis && hid) hid.value = vis.value.replace(/[^0-9]/g, '');
            });

            form.querySelectorAll('[required]').forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500');
                    field.classList.remove('border-gray-200');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500');
                    field.classList.add('border-gray-200');
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
                        this.classList.add('border-gray-200');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                    }
                });
            });
        });
    });
</script>
@endsection
