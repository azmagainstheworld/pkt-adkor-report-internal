@extends('layouts.app')

@section('content')
{{-- Load library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js">
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            @if(old('nama_perizinan') || old('nomor'))
                openModal('modalPerizinanTerbit');
            @elseif(old('nama_proses') || old('target'))
                openModal('modalPerizinanProses');
            @endif
        @endif
    });
</script>
{{-- Load Flatpickr untuk Year Picker --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr">
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            @if(old('nama_perizinan') || old('nomor'))
                openModal('modalPerizinanTerbit');
            @elseif(old('nama_proses') || old('target'))
                openModal('modalPerizinanProses');
            @endif
        @endif
    });
</script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js">
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            @if(old('nama_perizinan') || old('nomor'))
                openModal('modalPerizinanTerbit');
            @elseif(old('nama_proses') || old('target'))
                openModal('modalPerizinanProses');
            @endif
        @endif
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js">
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            @if(old('nama_perizinan') || old('nomor'))
                openModal('modalPerizinanTerbit');
            @elseif(old('nama_proses') || old('target'))
                openModal('modalPerizinanProses');
            @endif
        @endif
    });
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

<!-- Main Scrollable Content -->
@if(request('search'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchTerm = {!! json_encode(request('search')) !!}.toLowerCase();
        const root = document.querySelector('main');
        if (root) {
            const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
            let node;
            const nodesToReplace = [];
            
            while (node = walker.nextNode()) {
                if (node.nodeValue.toLowerCase().includes(searchTerm) && node.parentNode.nodeName !== 'SCRIPT' && node.parentNode.nodeName !== 'STYLE') {
                    nodesToReplace.push(node);
                }
            }
            
            let firstMark = null;
            nodesToReplace.forEach(n => {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                const span = document.createElement('span');
                span.innerHTML = n.nodeValue.replace(regex, '<mark class="bg-yellow-300 text-black px-1 rounded font-semibold">$1</mark>');
                n.parentNode.replaceChild(span, n);
                if (!firstMark) firstMark = span.querySelector('mark');
            });
            
            if (firstMark) {
                firstMark.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            @if(old('nama_perizinan') || old('nomor'))
                openModal('modalPerizinanTerbit');
            @elseif(old('nama_proses') || old('target'))
                openModal('modalPerizinanProses');
            @endif
        @endif
    });
</script>
@endif

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <x-success-modal />

    {{-- Notifikasi Error Validasi Backend --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm flex flex-col shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-bold">Gagal menyimpan data. Periksa inputan Anda:</span>
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
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Perizinan Perkantoran</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Perizinan Perkantoran</h2>
            <p class="text-sm text-gray-500">{{ $tanggalToday }}</p>
        </div>
        
        <!-- Filter Kanan Atas Dinamis -->
        <form action="{{ route('perizinan-perkantoran.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>

            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART SECTION ================= -->
    <x-card class="!rounded-xl overflow-hidden shadow-sm border border-gray-100 mb-8 p-6 bg-white">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Statistik Perizinan Terbit ({{ $filterTahun == 'semua' ? 'Semua Tahun' : $filterTahun }})</h3>
                <p class="text-xs text-gray-400">Distribusi 5 kategori kegiatan berdasarkan filter periode</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#A855F7]"></span><span class="text-gray-700 font-medium">Produk</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#2563EB]"></span><span class="text-gray-700 font-medium">Aset</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#EF4444]"></span><span class="text-gray-700 font-medium">Proyek</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#22C55E]"></span><span class="text-gray-700 font-medium">Peralatan Pabrik</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#F97316]"></span><span class="text-gray-700 font-medium">Adm & Lainnya</span></div>
            </div>
        </div>

        <div class="h-64 w-full relative">
            <canvas id="perizinanChart"></canvas>
        </div>
    </x-card>

    <!-- ================= TABEL 1: RINGKASAN AKUMULASI (SESUAI EXCEL) ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Ringkasan Kegiatan Perizinan Terbit</h3>
                <p class="text-xs text-gray-400">Akumulasi total terbit per kategori kegiatan sesuai periode filter</p>
            </div>
            <div class="text-xs text-gray-400" id="perizinan-ringkasan-info"></div>
        </div>

        <x-table :headers="['Tahun', 'Bulan', 'Produk', 'Aset', 'Proyek', 'Peralatan Pabrik', 'Adm & Lainnya', 'Total Perizinan Terbit']">
            @forelse($dataRingkasan as $index => $ringkasan)
                <tr class="perizinan-ringkasan-row {{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }} hover:bg-gray-100 transition-colors text-sm" style="display:none;">
                    <td class="px-6 py-4 text-gray-700 font-medium text-center align-middle">{{ $ringkasan['tahun'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['bulan'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['produk'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['aset'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['proyek'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['peralatan_pabrik'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['adm'] }}</td>
                    <td class="px-6 py-4 bg-blue-50 font-bold text-blue-900 text-center align-middle">{{ $ringkasan['total_terbit'] }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data ringkasan.</td></tr>
            @endforelse
        </x-table>

        <!-- Pagination Ringkasan Perizinan -->
        <div id="perizinan-ringkasan-pagination" class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 rounded-b-xl">
            <button id="perizinan-ringkasan-btn-prev"
                onclick="perizinanRingkasanChangePage(-1)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 bg-white hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                disabled>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Sebelumnya
            </button>
            <span id="perizinan-ringkasan-page-label" class="text-xs text-gray-500"></span>
            <button id="perizinan-ringkasan-btn-next"
                onclick="perizinanRingkasanChangePage(1)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 bg-white hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                disabled>
                Selanjutnya
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </x-card>

    <script>
    (function() {
        const ROWS_PER_PAGE = 6;
        let currentPage = 1;
        const rows = Array.from(document.querySelectorAll('.perizinan-ringkasan-row'));
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / ROWS_PER_PAGE);

        function renderPage(page) {
            const start = (page - 1) * ROWS_PER_PAGE;
            const end = start + ROWS_PER_PAGE;
            rows.forEach((row, i) => { row.style.display = (i >= start && i < end) ? '' : 'none'; });

            const label = document.getElementById('perizinan-ringkasan-page-label');
            const info = document.getElementById('perizinan-ringkasan-info');
            if (label) label.textContent = totalRows > 0 ? `Halaman ${page} dari ${totalPages}` : '';
            if (info) info.textContent = totalRows > 0 ? `${Math.min(end, totalRows)} dari ${totalRows} data` : '';

            document.getElementById('perizinan-ringkasan-btn-prev').disabled = (page <= 1);
            document.getElementById('perizinan-ringkasan-btn-next').disabled = (page >= totalPages);

            const pagination = document.getElementById('perizinan-ringkasan-pagination');
            if (pagination) pagination.style.display = (totalPages <= 1) ? 'none' : '';
        }

        window.perizinanRingkasanChangePage = function(delta) {
            currentPage = Math.max(1, Math.min(totalPages, currentPage + delta));
            renderPage(currentPage);
        };

        if (totalRows > 0) renderPage(1);
    })();
    </script>


    <!-- ================= TABEL 2: RINCIAN PERIZINAN (SESUAI EXCEL) ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 relative z-20">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Perizinan Terbit</h3>
                <p class="text-xs text-gray-400">Detail dokumen perizinan sesuai filter yang dipilih</p>
            </div>
            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperTerbit')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiSuperTerbit" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p></div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportPerizinanTerbit'); toggleActionDropdown('dropdownOpsiSuperTerbit')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import Excel
                            </button>
                            <a href="{{ route('perizinan-perkantoran.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Export Excel
                            </a>
                            <a href="{{ route('perizinan-perkantoran.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Export PDF
                            </a>
                        </div>
<div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p></div><div class="py-1" role="none"><button type="button" onclick="openModal('modalAturKolomTerbit'); toggleActionDropdown('dropdownOpsiSuperTerbit')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>Atur Kolom</button></div>
                        
                    </div>
                </div>
            
                <x-button variant="primary" onclick="openModalTambah()" class="shadow-sm text-xs border-none !py-2 !rounded-xl">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Rincian
                </x-button>
            </div>
        </div>

        <x-table :headers="['NO', 'Tahun', 'Perizinan Terbit', 'Nomor', 'Terbit', 'Berakhir', 'Instansi Penerbit', 'Bulan', 'Kegiatan', 'Aksi']">
            @forelse ($dataRincian as $index => $rincian)
                <tr class="{{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }} hover:bg-gray-100 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-700 font-medium text-center align-middle">{{ $dataRincian->firstItem() + $index }}</td>
                    
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ Carbon\Carbon::parse($rincian->tanggal_sejak)->year }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-normal break-words text-center align-middle" title="{{ $rincian->nama_perizinan }}">{{ $rincian->nama_perizinan ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 font-mono text-xs text-center align-middle">{{ $rincian->nomor }}</td>
                    {{-- Format Tanggal Indonesia (Contoh: 15 Januari 2024) --}}
                    @php
                        $bulanPendek = [
                            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                            7 => 'Jul', 8 => 'Agustus', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                        ];
                        $tglSejak = Carbon\Carbon::parse($rincian->tanggal_sejak);
                        $formatSejak = $tglSejak->format('d-') . $bulanPendek[$tglSejak->month] . $tglSejak->format('-y');
                        
                        $tglAkhir = Carbon\Carbon::parse($rincian->tanggal_akhir);
                        $formatAkhir = $tglAkhir->format('d-') . $bulanPendek[$tglAkhir->month] . $tglAkhir->format('-y');
                    @endphp
                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-center align-middle">{{ $formatSejak }}</td>
                    <td class="px-6 py-4 text-red-500 font-medium whitespace-nowrap text-center align-middle">{{ $formatAkhir }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle" title="{{ $rincian->instansi_penerbit }}">{{ $rincian->instansi_penerbit }}</td>
                    <td class="px-6 py-4 text-gray-600 font-medium bg-red-50/50 text-center align-middle">
                        @php
                            $bulanPanjang = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        {{ $bulanPanjang[$tglSejak->month] }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $rincian->kegiatan }}</td>

                    <td class="px-6 py-4 align-middle">
                        <div class="flex gap-2 justify-center">
                            <button type="button" 
                                onclick="openModalEdit({
                                    id: '{{ $rincian->id }}',
                                    nama_perizinan: '{{ addslashes($rincian->nama_perizinan) }}',
                                    kegiatan: '{{ $rincian->kegiatan }}',
                                    nomor: '{{ addslashes($rincian->nomor) }}',
                                    tanggal_sejak: '{{ Carbon\Carbon::parse($rincian->tanggal_sejak)->format('Y-m') }}',
                                    tanggal_akhir: '{{ Carbon\Carbon::parse($rincian->tanggal_akhir)->format('Y-m') }}',
                                    instansi_penerbit: '{{ addslashes($rincian->instansi_penerbit) }}'
                                })" 
                                class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button type="button" onclick="openDeleteModal('modalHapusPerizinan', '/perizinan-perkantoran/{{ $rincian->id }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data rincian untuk periode ini.</td></tr>
            @endforelse
        </x-table>
        
        <div class="p-4 border-t border-gray-100 text-xs bg-white">
            {{ $dataRincian->links() }}
        </div>
    </x-card>

    <!-- ================= TABEL 3: PERIZINAN PROSES (SESUAI EXCEL MERGED) ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 relative z-20">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Perizinan Proses</h3>
                <p class="text-xs text-gray-400">Pemantauan progres perizinan yang masih berjalan</p>
            </div>
            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperProses')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiSuperProses" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p></div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportPerizinanProses'); toggleActionDropdown('dropdownOpsiSuperProses')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import Excel
                            </button>
                            <a href="{{ route('perizinan-proses.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Export Excel
                            </a>
                            <a href="{{ route('perizinan-proses.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Export PDF
                            </a>
                        </div>
<div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p></div><div class="py-1" role="none"><button type="button" onclick="openModal('modalAturKolomProses'); toggleActionDropdown('dropdownOpsiSuperProses')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>Atur Kolom</button></div>
                        
                    </div>
                </div>

                <x-button variant="primary" onclick="openModalTambahProses()" class="shadow-sm text-xs border-none !py-2 !rounded-xl">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Proses
                </x-button>
            </div>
        </div>

        <x-table :headers="['Tahun', 'No.', 'Perizinan Proses', 'Target', 'Periode (Bulan)', 'Aksi']">
            @php $no = $dataProses->firstItem(); @endphp
            
            @forelse($groupedProses as $group)
                @php $rowspan = $group->count(); @endphp
                
                @foreach($group as $index => $proses)
                    <tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-200">
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 text-gray-700 font-medium bg-gray-50/50 align-top border-r border-gray-200 text-center">{{ $proses->tahun }}</td>
                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 text-gray-700 font-medium bg-gray-50/50 align-top border-r border-gray-200 text-center">{{ $no++ }}</td>
                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 text-gray-900 font-bold align-top border-r border-gray-200 text-center">{{ $proses->nama_proses }}</td>
                        @endif
                        
                        <td class="px-6 py-4 text-gray-700 align-top text-center {{ $index > 0 ? 'border-t border-gray-100' : '' }}">{!! nl2br(e($proses->target)) !!}</td>
                        
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 text-gray-600 font-medium align-top border-l border-r border-gray-200 text-center">{{ $proses->periode }}</td>
                        @endif

                        <td class="px-6 py-4 align-top border-l border-gray-200 text-center {{ $index > 0 ? 'border-t border-gray-100' : '' }}">
                            <div class="flex gap-2 justify-center">
                                <button type="button" 
                                    onclick="openModalEditProses({
                                        id: '{{ $proses->id }}',
                                        tahun: '{{ $proses->tahun }}',
                                        nama_proses: '{{ addslashes($proses->nama_proses) }}',
                                        target: '{{ addslashes($proses->target) }}',
                                        periode: '{{ addslashes($proses->periode) }}'
                                    })" 
                                    class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Target">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusProses', '/perizinan-proses-list/{{ $proses->id }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Target">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data perizinan proses.</td></tr>
            @endforelse
        </x-table>
    </x-card>

    <x-delete-modal id="modalHapusPerizinan" title="Hapus Data Perizinan Terbit" message="Apakah Anda yakin ingin menghapus data perizinan terbit ini? Data yang dihapus tidak dapat dikembalikan." />
    <x-delete-modal id="modalHapusProses" title="Hapus Data Perizinan Proses" message="Apakah Anda yakin ingin menghapus progres perizinan ini? Data yang dihapus tidak dapat dikembalikan." />

    <!-- ================= MODAL TAMBAH / EDIT PERIZINAN TERBIT ================= -->
    <x-modal id="modalTambahRincian" title="Formulir Rincian Perizinan Terbit" description="Lengkapi detail dokumen perizinan perkantoran.">
        <form action="{{ route('perizinan-perkantoran.store') }}" method="POST" id="formPerizinan" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Perizinan Terbit <span class="text-red-500">*</span></label>
                <input type="text" name="nama_perizinan" id="form_nama_perizinan" required placeholder="Contoh: Pendaftaran NPP NPK" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bidang ini wajib diisi!</span>
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kegiatan <span class="text-red-500">*</span></label>
                <select name="kegiatan" id="form_kegiatan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 transition-colors">
                    <option value="" disabled selected>Pilih Kegiatan...</option>
                    <option value="Produk">Produk</option>
                    <option value="Aset">Aset</option>
                    <option value="Proyek">Proyek</option>
                    <option value="Peralatan Pabrik">Peralatan Pabrik</option>
                    <option value="Adm & Lainnya">Adm & Lainnya</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Kegiatan wajib dipilih!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Perizinan <span class="text-red-500">*</span></label>
                <input type="text" name="nomor" id="form_nomor" required placeholder="Contoh: PB-UMKU: 8120..." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Nomor wajib diisi!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Terbit (Tanggal) <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_sejak" id="form_tanggal_sejak" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Tanggal terbit wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Berakhir (Tanggal) <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_akhir" id="form_tanggal_akhir" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Tanggal berakhir wajib dipilih!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Instansi Penerbit <span class="text-red-500">*</span></label>
                <input type="text" name="instansi_penerbit" id="form_instansi_penerbit" required placeholder="Contoh: Kementerian Pertanian RI" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Instansi wajib diisi!</span>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-4 border-t border-gray-100 pt-5">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahRincian')" class="!rounded-xl !py-2.5 text-sm !px-6">Batal</x-button>
                <x-button variant="primary" type="submit" class="border-none shadow-md !py-2.5 text-sm !px-6">Simpan Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH / EDIT PERIZINAN PROSES ================= -->
    <x-modal id="modalTambahProses" title="Formulir Perizinan Proses" description="Masukkan atau perbarui tahapan perizinan yang sedang diproses.">
        <form action="{{ route('perizinan-proses.store') }}" method="POST" id="formProses" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodFieldProses" value="POST">
            
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                {{-- Input diubah ke type="text" agar flatpickr bekerja maksimal --}}
                <input type="text" name="tahun" id="form_proses_tahun" required placeholder="Pilih Tahun" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors cursor-pointer">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Tahun wajib diisi!</span>
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan)</label>
                <input type="text" name="periode" id="form_proses_periode" placeholder="Pilih Periode" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors cursor-pointer">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Perizinan Proses <span class="text-red-500">*</span></label>
                <input type="text" name="nama_proses" id="form_proses_nama" required placeholder="Contoh: Project Papua Barat" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Nama Proses wajib diisi!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Target</label>
                <textarea name="target" id="form_proses_target" rows="3" placeholder="Contoh: 1. AMDAL: Proses Pertek..." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors"></textarea>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-4 border-t border-gray-100 pt-5">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahProses')" class="!rounded-xl !py-2.5 text-sm !px-6">Batal</x-button>
                <x-button variant="primary" type="submit" class="border-none shadow-md !py-2.5 text-sm !px-6">Simpan Proses</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL IMPORT ================= -->
    <x-import-modal 
        id="modalImportPerizinanTerbit" 
        route="{{ route('perizinan-perkantoran.import') }}" 
        title="Import Data Perizinan Terbit" 
        templateRoute="{{ route('template.download', 'perizinan-terbit') }}" 
    />

    <x-import-modal 
        id="modalImportPerizinanProses" 
        route="{{ route('perizinan-proses.import') }}" 
        title="Import Data Perizinan Proses" 
        templateRoute="{{ route('template.download', 'perizinan-proses') }}" 
    />
    <!-- ================= MODAL ATUR KOLOM TERBIT ================= -->
    <x-modal id="modalAturKolomTerbit" title="Atur Kolom (Perizinan Terbit)" description="Tambahkan atau hapus kolom tambahan untuk tabel perizinan terbit.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Saat Ini:</h4>
            @if(isset($kolomDinamisTerbit) && $kolomDinamisTerbit->count() > 0)
                @foreach($kolomDinamisTerbit as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown, true)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('modalAturKolomTerbit', '{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom dinamis tambahan.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="perizinan_terbit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputTerbit" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('tipeInputTerbit', 'dropdownConfigAreaTerbit')">
                        <option value="text">Teks Singkat</option><option value="number">Angka</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigAreaTerbit">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Tinggi, Sedang, Rendah" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolomTerbit')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>

    <!-- ================= MODAL ATUR KOLOM PROSES ================= -->
    <x-modal id="modalAturKolomProses" title="Atur Kolom (Perizinan Proses)" description="Tambahkan atau hapus kolom tambahan untuk tabel perizinan proses.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Saat Ini:</h4>
            @if(isset($kolomDinamisProses) && $kolomDinamisProses->count() > 0)
                @foreach($kolomDinamisProses as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown, true)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('modalAturKolomProses', '{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom dinamis tambahan.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="perizinan_proses_list">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputProses" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('tipeInputProses', 'dropdownConfigAreaProses')">
                        <option value="text">Teks Singkat</option><option value="number">Angka</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigAreaProses">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Tinggi, Sedang, Rendah" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolomProses')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>
</main>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // ----------------------------------------------------
    // Inisialisasi Flatpickr Year & Periode
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#form_proses_tahun", {
            locale: "id",
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "Y",
                    altFormat: "Y",
                    theme: "light"
                })
            ]
        });

        flatpickr("#form_proses_periode", {
            locale: "id",
            plugins: [
                new monthSelectPlugin({
                    shorthand: false,
                    dateFormat: "F Y",
                    altFormat: "F Y",
                    theme: "light"
                })
            ]
        });
    });

    // ----------------------------------------------------
    // LOGIKA PERIZINAN TERBIT
    // ----------------------------------------------------
    const formPerizinan = document.getElementById('formPerizinan');
    const modalTitle = document.querySelector('#modalTambahRincian h3');
    const methodField = document.getElementById('methodField');

    function resetValidationTerbit() {
        const requiredFields = formPerizinan.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.classList.remove('border-red-500', 'bg-red-50');
            const errorSpan = field.nextElementSibling;
            if(errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
        });
    }

    function openModalTambah() {
        modalTitle.textContent = "Tambah Rincian Perizinan Terbit";
        formPerizinan.action = "{{ route('perizinan-perkantoran.store') }}";
        methodField.value = "POST";
        formPerizinan.reset();
        resetValidationTerbit();
        openModal('modalTambahRincian');
    }

    function openModalEdit(data) {
        modalTitle.textContent = "Edit Rincian Perizinan Terbit";
        formPerizinan.action = "/perizinan-perkantoran/" + data.id; 
        methodField.value = "PUT";
        
        document.getElementById('form_nama_perizinan').value = data.nama_perizinan;
        document.getElementById('form_kegiatan').value = data.kegiatan;
        document.getElementById('form_nomor').value = data.nomor;
        document.getElementById('form_tanggal_sejak').value = data.tanggal_sejak;
        document.getElementById('form_tanggal_akhir').value = data.tanggal_akhir;
        document.getElementById('form_instansi_penerbit').value = data.instansi_penerbit;

        resetValidationTerbit();
        openModal('modalTambahRincian');
    }

    // ----------------------------------------------------
    // LOGIKA PERIZINAN PROSES
    // ----------------------------------------------------
    const formProses = document.getElementById('formProses');
    const modalTitleProses = document.querySelector('#modalTambahProses h3');
    const methodFieldProses = document.getElementById('methodFieldProses');

    function resetValidationProses() {
        const requiredFields = formProses.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.classList.remove('border-red-500', 'bg-red-50');
            const errorSpan = field.nextElementSibling;
            if(errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
        });
    }

    function openModalTambahProses() {
        modalTitleProses.textContent = "Tambah Perizinan Proses";
        formProses.action = "{{ route('perizinan-proses.store') }}";
        methodFieldProses.value = "POST";
        formProses.reset();
        
        // Set tahun default ke tahun sekarang pada Flatpickr
        document.getElementById('form_proses_tahun')._flatpickr.setDate(new Date().getFullYear().toString());
        document.getElementById('form_proses_periode')._flatpickr.clear();
        
        resetValidationProses();
        openModal('modalTambahProses');
    }

    function openModalEditProses(data) {
        modalTitleProses.textContent = "Edit Perizinan Proses";
        formProses.action = "/perizinan-proses-list/" + data.id; 
        methodFieldProses.value = "PUT";
        
        // Set tahun dan periode pada Flatpickr
        document.getElementById('form_proses_tahun')._flatpickr.setDate(data.tahun);
        if (data.periode) {
            document.getElementById('form_proses_periode')._flatpickr.setDate(data.periode);
        } else {
            document.getElementById('form_proses_periode')._flatpickr.clear();
        }
        
        document.getElementById('form_proses_nama').value = data.nama_proses;
        document.getElementById('form_proses_target').value = data.target;

        resetValidationProses();
        openModal('modalTambahProses');
    }

    // ----------------------------------------------------
    // VALIDASI MERAH CLIENT SIDE (KEDUA FORM)
    // ----------------------------------------------------
    [formPerizinan, formProses].forEach(form => {
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
                    const errorSpan = field.nextElementSibling;
                    if (!field.value || field.value.trim() === '') {
                        isValid = false;
                        field.classList.add('border-red-500', 'bg-red-50'); 
                        field.classList.remove('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden'); 
                    } else {
                        field.classList.remove('border-red-500', 'bg-red-50');
                        field.classList.add('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden'); 
                    }
                });

                if (!isValid) e.preventDefault(); 
            });

            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                // Gunakan event 'change' juga untuk menangkap input dari flatpickr
                ['input', 'change'].forEach(evt => {
                    field.addEventListener(evt, function() {
                        const errorSpan = this.nextElementSibling;
                        if (this.value && this.value.trim() !== '') {
                            this.classList.remove('border-red-500', 'bg-red-50');
                            this.classList.add('border-gray-300');
                            if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                        }
                    });
                });
            });
        }
    });

    // ----------------------------------------------------
    // LOGIKA CHART.JS DINAMIS
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        const canvasPerizinan = document.getElementById('perizinanChart');
        if (canvasPerizinan) {
            const ctx = canvasPerizinan.getContext('2d');
            const rawChartData = {!! json_encode($chartData) !!};
            const labels = rawChartData.map(d => d.label);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Produk', data: rawChartData.map(d => d.produk), backgroundColor: '#A855F7', barPercentage: 0.5, categoryPercentage: 0.7 },
                        { label: 'Aset', data: rawChartData.map(d => d.aset), backgroundColor: '#2563EB', barPercentage: 0.5, categoryPercentage: 0.7 },
                        { label: 'Proyek', data: rawChartData.map(d => d.proyek), backgroundColor: '#EF4444', barPercentage: 0.5, categoryPercentage: 0.7 },
                        { label: 'Peralatan Pabrik', data: rawChartData.map(d => d.peralatan_pabrik), backgroundColor: '#22C55E', barPercentage: 0.5, categoryPercentage: 0.7 },
                        { label: 'Adm & Lainnya', data: rawChartData.map(d => d.adm), backgroundColor: '#F97316', barPercentage: 0.5, categoryPercentage: 0.7 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { stacked: true, beginAtZero: true, grid: { color: '#F3F4F6', drawBorder: false } },
                        x: { stacked: true, grid: { display: false, drawBorder: false } }
                    }
                }
            });
        }
    });

    // ----------------------------------------------------
    // LOGIKA DROPDOWN OPSI LANJUTAN
    // ----------------------------------------------------
    function toggleActionDropdown(id) {
        const dd = document.getElementById(id);
        if(dd.classList.contains('hidden')) {
            document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(el => el.classList.add('hidden'));
            dd.classList.remove('hidden');
        } else {
            dd.classList.add('hidden');
        }
    }
    
    document.addEventListener('click', function(e) {
        if(!e.target.closest('.relative.inline-block.text-left')) {
            document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(el => el.classList.add('hidden'));
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            @if(old('nama_perizinan') || old('nomor'))
                openModal('modalPerizinanTerbit');
            @elseif(old('nama_proses') || old('target'))
                openModal('modalPerizinanProses');
            @endif
        @endif
    });
</script>
@endsection