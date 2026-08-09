@extends('layouts.app')

@section('content')
<!-- Tambahkan Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <!-- Panggil Modal Notifikasi Sukses -->
    <x-success-modal />

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Anggaran</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Anggaran Administrasi Korporat</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
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

    <!-- Tombol Tambah Data (di atas ketiga tabel) -->
    <div class="flex justify-end mb-4">
        <x-button variant="primary" onclick="openModalTambah()" class="!rounded-xl !text-xs !py-2.5 !px-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Laporan Bulan Ini
        </x-button>
    </div>

    <!-- ================= TABEL 1: RINCIAN PER BULAN, DIKELOMPOKKAN PER KATEGORI ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Rincian Realisasi Penggunaan dan Sisa Anggaran</h3>
            <p class="text-sm text-gray-600">
                Realisasi Penggunaan dan Sisa untuk Anggaran Dikelola, Anggaran Rutin dan Anggaran Investasi yang dikelola
                oleh Departemen Administrasi Korporat — {{ $filterLabel }} sebagai berikut:
            </p>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['Tahun', 'Bulan', 'Detail', 'Anggaran RKAP', 'Komitmen', 'Realisasi', 'Realisasi + Komitmen', '% Realisasi+Komitmen', 'Sisa Anggaran', '% Sisa Anggaran']">
                @forelse ($detailTable as $entry)
                    @foreach ($entry['kategori'] as $kat => $items)
                        <!-- Baris Header Kategori -->
                        <tr class="bg-blue-50/70">
                            <td class="px-6 py-2.5 text-gray-700 font-semibold text-center align-middle whitespace-nowrap">{{ $entry['tahun'] }}</td>
                            <td class="px-6 py-2.5 text-gray-700 font-semibold text-center align-middle whitespace-nowrap">{{ $entry['bulan'] }}</td>
                            <td class="px-6 py-2.5 font-extrabold text-blue-900 text-left align-middle" colspan="8">{{ $kategoriList[$kat] }}</td>
                        </tr>

                        <!-- Baris Detail -->
                        @foreach ($items as $anggaran)
                            @php
                                $realPlusKomit = $anggaran->komitmen + $anggaran->realisasi;
                                $sisaAnggaran = $anggaran->rkap - $realPlusKomit;
                                $percRk = ($anggaran->rkap > 0) ? ($realPlusKomit / $anggaran->rkap) * 100 : 0;
                                $percSisa = ($anggaran->rkap > 0) ? ($sisaAnggaran / $anggaran->rkap) * 100 : 0;

                                // Logika Pewarnaan Kolom % R+K Otomatis (replikasi warna Looker)
                                if ($percRk > 80) { $rkColor = "bg-red-900/10 text-red-900"; }
                                elseif ($percRk >= 50) { $rkColor = "bg-orange-100 text-orange-700"; }
                                else { $rkColor = "bg-yellow-100 text-yellow-700"; }
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
                            </tr>
                        @endforeach
                    @endforeach
                @empty
                    <tr><td colspan="10" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data anggaran untuk filter yang dipilih.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINGKASAN TOTAL GABUNGAN PER BULAN ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
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
    </x-card>

    <!-- ================= TABEL 3: PERBANDINGAN SISA ANGGARAN ANTAR KATEGORI ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
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
    </x-card>

    <!-- ==========================================
          MODAL TAMBAH DATA ANGGARAN (FORMULIR INPUT)
          (Implementasi Constraint Validasi, Money Format, Angka Saja)
    =========================================== -->
    <x-modal id="modalTambahAnggaran" title="Tambah Laporan Anggaran Bulanan Korporat" description="Masukkan data mentah anggaran untuk bulan laporan. Kalkulasi persentase dan sisa akan dilakukan otomatis oleh sistem.">
        <form id="formTambahAnggaran" action="{{ route('anggaran.store') }}" method="POST" novalidate>
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                <!-- Field Periode (Tahun & Bulan) — pakai kalender bawaan browser untuk minimalisir human error -->
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
                           class="w-full px-4 py-2.5 bg-gray-50 border {{ ($errors->has('tahun') || $errors->has('bulan')) ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-200' }} rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner cursor-pointer">

                    {{-- Tampilan konfirmasi dalam Bahasa Indonesia, karena format kalender bawaan browser bisa saja tampil dalam Bahasa Inggris --}}
                    <p id="periode_display" class="text-xs text-gray-500 mt-1.5">Terpilih: <span class="font-semibold text-gray-700">-</span></p>

                    {{-- Hidden input yang benar-benar dikirim ke server, sesuai format kolom 'tahun' & 'bulan' di database --}}
                    <input type="hidden" name="tahun" id="tahun_hidden" value="{{ old('tahun') }}">
                    <input type="hidden" name="bulan" id="bulan_hidden" value="{{ old('bulan') }}">

                    @error('tahun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('bulan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field Kategori Anggaran (Dikelola / Rutin / Investasi) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori Anggaran <span class="text-red-500">*</span></label>
                    <select name="kategori" required class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('kategori') ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-200' }} rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                        <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih Kategori</option>
                        @foreach ($kategoriList as $key => $label)
                            <option value="{{ $key }}" {{ old('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('kategori')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field Detail Anggaran -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama/Detail Pos Anggaran <span class="text-red-500">*</span></label>
                    <input type="text" name="detail_anggaran" required value="{{ old('detail_anggaran') }}" placeholder="Contoh: Cetak dan Fotocopy" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('detail_anggaran') ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-200' }} rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                    @error('detail_anggaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field Anggaran RKAP (Implementasi Money Format & Angka Saja) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Anggaran RKAP <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="text"
                               id="rkap_input"
                               required
                               value="{{ old('rkap') ? number_format((float) old('rkap'), 0, ',', '.') : '' }}"
                               placeholder="1.000.000"
                               class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('rkap') ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-200' }} rounded-lg text-sm text-gray-900 font-mono font-extrabold focus:outline-none focus:border-pkt-biru focus:ring-1 focus:ring-pkt-biru-terang shadow-inner transition-all hover:border-pkt-biru">
                        {{-- Hidden input untuk mengirim nilai integer mentah --}}
                        <input type="hidden" name="rkap" id="rkap_hidden" value="{{ old('rkap') }}">
                    </div>
                    @error('rkap')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                               class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('komitmen') ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-200' }} rounded-lg text-sm text-gray-900 font-mono focus:outline-none focus:border-pkt-biru focus:ring-1 focus:ring-pkt-biru-terang shadow-inner hover:border-pkt-biru transition-all">
                        <input type="hidden" name="komitmen" id="komitmen_hidden" value="{{ old('komitmen') }}">
                    </div>
                    @error('komitmen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                               class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('realisasi') ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-200' }} rounded-lg text-sm text-gray-900 font-mono focus:outline-none focus:border-pkt-biru focus:ring-1 focus:ring-pkt-biru-terang shadow-inner hover:border-pkt-biru transition-all">
                        <input type="hidden" name="realisasi" id="realisasi_hidden" value="{{ old('realisasi') }}">
                    </div>
                    @error('realisasi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan Tambahan (Wajib Diisi)</label>
                    <textarea name="keterangan" required rows="3" placeholder="Contoh: 'Penggunaan Sisa RKAP' atau 'Dibutuhkan data Karyawan untuk update database'" class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('keterangan') ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-200' }} rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="flex justify-end gap-3 mt-8 border-t border-gray-100 pt-6">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahAnggaran')" class="!rounded-xl !text-xs !py-2.5 !px-5">Batal</x-button>
                <x-button variant="primary" type="submit" class="!rounded-xl !text-xs !py-2.5 !px-5">Simpan Laporan Bulanan</x-button>
            </div>
        </form>
    </x-modal>

</main>

{{-- ================= CHARTS JAVASCRIPT CONFIGURATION ================= --}}
<script>
    Chart.register(ChartDataLabels);

    // Berisi konfigurasi untuk SEMUA chart kategori (Dikelola, Rutin, Investasi)
    const allChartData = {!! json_encode($chartConfig) !!};

    // Menyimpan instance Chart.js yang sedang aktif per chart, supaya bisa diganti tipenya (pie/bar/doughnut)
    const chartInstances = {};

    // Warna abu-abu netral untuk kondisi "belum ada data"
    const EMPTY_CHART_COLOR = '#E5E7EB';

    function buildChartData(config) {
        // Kalau tidak ada data sama sekali (RKAP = 0), tetap render lingkaran/bar abu-abu
        // penuh dengan label "Tidak ada data", bukan kotak kosong melompong.
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

        // Hancurkan instance lama dulu kalau sedang ganti tipe chart
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
                        // Legend per-dataset pada Bar Chart tidak berguna (dan menyebabkan label "undefined"
                        // karena dataset tidak diberi nama) — kategori sudah jelas dari sumbu-X + angka %.
                        // Untuk Pie/Doughnut, legend tetap ditampilkan (termasuk saat kosong, supaya teks
                        // "Tidak ada data" tetap terlihat karena tidak ada sumbu-X di tipe chart ini).
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

    // Dipanggil otomatis oleh komponen x-dynamic-chart saat user ganti pilihan
    // di dropdown "Pie Chart / Bar Chart / Doughnut" milik masing-masing kartu.
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
    // -- Fungsi Buka/Tutup Modal --
    function openModalTambah() {
        document.getElementById('modalTambahAnggaran').classList.remove('hidden');
    }
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // -- Otomatis buka kembali modal Tambah Data jika validasi gagal (ada error) --
    @if ($errors->any())
        openModalTambah();
    @endif

    // -- SINKRONISASI PERIODE (input kalender type=month) ke hidden field tahun & bulan --
    // Kolom 'bulan' di database disimpan berupa nama Bahasa Indonesia (Januari, Februari, dst),
    // jadi nilai "YYYY-MM" dari kalender bawaan browser perlu dikonversi dulu.
    const bulanIndoList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    const periodeInput = document.getElementById('periode_input');
    const tahunHidden = document.getElementById('tahun_hidden');
    const bulanHidden = document.getElementById('bulan_hidden');
    const periodeDisplay = document.getElementById('periode_display');

    function syncPeriode() {
        const val = periodeInput.value; // format: "YYYY-MM"
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
    syncPeriode(); // sinkronisasi awal saat modal pertama kali render (termasuk saat re-open akibat error validasi)

    // -- MONEY FORMATTER --
    const moneyInputIds = ['rkap_input', 'komitmen_input', 'realisasi_input'];

    moneyInputIds.forEach(inputId => {
        const inputEl = document.getElementById(inputId);
        const hiddenEl = document.getElementById(inputId.replace('_input', '_hidden'));

        // format visual saat input diketik
        inputEl.addEventListener('input', function (e) {
            // hapus karakter non-angka
            let rawValue = this.value.replace(/[^0-9]/g, '');

            // Jangan sampai bisa menerima angka 0 di depan kecuali '0' mentah
            if (rawValue.length > 1 && rawValue.startsWith('0')) {
                rawValue = rawValue.replace(/^0+/, '');
            }

            // Simpan nilai mentah ke hidden input (integer untuk DB)
            hiddenEl.value = rawValue;

            if (rawValue) {
                this.value = new Intl.NumberFormat('id-ID').format(rawValue);
            } else {
                this.value = '';
            }
        });

        inputEl.addEventListener('blur', function () {
            hiddenEl.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    document.getElementById('formTambahAnggaran').addEventListener('submit', function (e) {
        // jaga-jaga: pastikan periode (tahun/bulan) tersinkron sebelum dikirim
        syncPeriode();

        // bersihkan titik untuk pengecekan validasi integer mentah
        moneyInputIds.forEach(inputId => {
            document.getElementById(inputId.replace('_input', '_hidden')).value = document.getElementById(inputId).value.replace(/[^0-9]/g, '');
        });
    });
</script>
@endsection