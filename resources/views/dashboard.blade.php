@extends('layouts.app')

@section('content')
<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-gray-50">

    <!-- Welcome Title -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Selamat datang, {{ auth()->user()->name }}</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->locale('en')->translatedFormat('l, d F Y') }}</p>
        </div>
        <span class="text-sm font-semibold text-blue-600 bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm">
            Ringkasan periode {{ $bulanIni }} {{ $tahunIni }}
        </span>
    </div>

    <!-- ======================================================= -->
    <!-- SECTION: KPI RINGKASAN UTAMA -->
    <!-- ======================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-5 mb-8">

        <!-- Card 1: Karyawan -->
        <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Karyawan</p>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $karyawanStats['total'] }}</p>
                <p class="text-[11px] text-gray-400">Organik: {{ $karyawanStats['organik'] }} | Non Organik: {{ $karyawanStats['nonOrganik'] }}</p>
            </div>
        </x-card>

        <!-- Card 2: Ketidakhadiran -->
        <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Ketidakhadiran</p>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $totalKetidakhadiran }}</p>
                <p class="text-[11px] text-gray-400">Total bulan {{ $bulanIni }}</p>
            </div>
        </x-card>

        <!-- Card 3: Anggaran -->
        <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 font-semibold">Rp</div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Anggaran</p>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $anggaranStats['persenTotal'] }}%</p>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min($anggaranStats['persenTotal'], 100) }}%"></div>
                </div>
                <p class="text-[11px] text-gray-400">Realisasi+Komitmen bulan ini</p>
            </div>
        </x-card>

        <!-- Card 4: Perizinan -->
        <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Perizinan Terbit</p>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $perizinanStats['bulanIni'] }}</p>
                <p class="text-[11px] text-gray-400">Terbit bulan {{ $bulanIni }}</p>
            </div>
        </x-card>

        <!-- Card 5: Pelaporan -->
        <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Pelaporan</p>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $pelaporanStats['total'] }}</p>
                <p class="text-[11px] text-gray-400">Eksternal: {{ $pelaporanStats['eksternal'] }} | Internal: {{ $pelaporanStats['internal'] }}</p>
            </div>
        </x-card>

        <!-- Card 6: Program Strategis -->
        <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Program Strategis</p>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $programStrategisTotal }}</p>
                <p class="text-[11px] text-gray-400">Kegiatan bulan {{ $bulanIni }}</p>
            </div>
        </x-card>
    </div>

    <!-- ======================================================= -->
    <!-- SECTION: KEARSIPAN & ADMINISTRASI (RINCIAN PER SUB-MENU) -->
    <!-- ======================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Kearsipan Detail -->
        <x-card class="p-5 bg-white border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">Kearsipan</h3>
                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded-md">Total: {{ $kearsipanStats['total'] }}</span>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">PA Non Teknik (Tekstual)</p>
                    <p class="text-xl font-bold text-gray-900">{{ $kearsipanStats['paTekstual'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">PA Non Teknik (Non Tekstual)</p>
                    <p class="text-xl font-bold text-gray-900">{{ $kearsipanStats['paNonTekstual'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">PA Teknik</p>
                    <p class="text-xl font-bold text-gray-900">{{ $kearsipanStats['paTeknik'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">Digital Office (DOF)</p>
                    <p class="text-xl font-bold text-gray-900">{{ $kearsipanStats['dof'] }}</p>
                </div>
            </div>
            <canvas id="chartKearsipan" height="140"></canvas>
        </x-card>

        <!-- Administrasi Detail -->
        <x-card class="p-5 bg-white border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">Administrasi Perkantoran</h3>
                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded-md">Total: {{ $administrasiStats['total'] }}</span>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">Pemeliharaan</p>
                    <p class="text-xl font-bold text-gray-900">{{ $administrasiStats['pemeliharaan'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">Pengiriman Dokumen</p>
                    <p class="text-xl font-bold text-gray-900">{{ $administrasiStats['pengiriman'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">Jasa Kurir</p>
                    <p class="text-xl font-bold text-gray-900">{{ $administrasiStats['kurir'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">Fotocopy</p>
                    <p class="text-xl font-bold text-gray-900">{{ $administrasiStats['fotocopy'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">Undangan</p>
                    <p class="text-xl font-bold text-gray-900">{{ $administrasiStats['undangan'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500">Surat Masuk & Keluar</p>
                    <p class="text-xl font-bold text-gray-900">{{ $administrasiStats['surat'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 col-span-2">
                    <p class="text-[11px] text-gray-500">BAR SK Memo (Terbit)</p>
                    <p class="text-xl font-bold text-gray-900">{{ $administrasiStats['barSkMemo'] }}</p>
                </div>
            </div>
            <canvas id="chartAdministrasi" height="140"></canvas>
        </x-card>
    </div>

    <!-- ======================================================= -->
    <!-- SECTION: ANGGARAN PER KATEGORI -->
    <!-- ======================================================= -->
    <div class="mb-8">
        <h3 class="font-bold text-gray-900 mb-4">Anggaran per Kategori — {{ $bulanIni }} {{ $tahunIni }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @forelse($anggaranStats['perKategori'] as $kode => $kat)
            <x-card class="p-5 bg-white border border-gray-100">
                <p class="text-sm font-semibold text-gray-700 mb-2">{{ $kat['label'] }}</p>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $kat['persen'] }}%</p>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-3 overflow-hidden">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ min($kat['persen'], 100) }}%"></div>
                </div>
                <div class="text-[11px] text-gray-500 space-y-0.5">
                    <p>RKAP: Rp {{ number_format($kat['rkap'], 0, ',', '.') }}</p>
                    <p>Realisasi+Komitmen: Rp {{ number_format($kat['realisasi_komitmen'], 0, ',', '.') }}</p>
                    <p class="font-semibold text-gray-700">Sisa: Rp {{ number_format($kat['sisa'], 0, ',', '.') }}</p>
                </div>
            </x-card>
            @empty
            <p class="text-sm text-gray-400 col-span-3">Belum ada data anggaran untuk periode ini.</p>
            @endforelse
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- SECTION: GRAFIK TAMBAHAN -->
    <!-- ======================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <x-card class="p-5 bg-white border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Komposisi Karyawan</h3>
            <canvas id="chartKaryawan" height="180"></canvas>
        </x-card>
        <x-card class="p-5 bg-white border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Proyeksi Pensiun Karyawan</h3>
            <canvas id="chartPensiun" height="180"></canvas>
        </x-card>
        <x-card class="p-5 bg-white border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Perizinan Terbit per Tahun</h3>
            <canvas id="chartPerizinanTahun" height="180"></canvas>
        </x-card>
    </div>

    <!-- ======================================================= -->
    <!-- SECTION: PANEL ALERT & PEKERJAAN TERTUNDA -->
    <!-- ======================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Panel 1: Perizinan Akan Kedaluwarsa -->
        <x-card class="!p-0 overflow-visible shadow-sm border border-red-100 flex flex-col h-full">
            <div class="px-5 py-4 border-b border-gray-100 bg-red-50/50 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                    <h3 class="font-bold text-gray-900">Perizinan Segera Kedaluwarsa</h3>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded-md">30 Hari Kedepan</span>
            </div>

            <div class="p-0 flex-1">
                @forelse($alertPerizinan as $izin)
                    @php
                        $sisaHari = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($izin->tanggal_akhir));
                    @endphp
                    <div class="px-5 py-3 border-b border-gray-50 hover:bg-gray-50 flex items-center justify-between transition-colors last:border-0">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $izin->nomor }}</p>
                            <p class="text-xs text-gray-500 truncate max-w-[250px]">{{ $izin->instansi_penerbit }} - {{ $izin->kegiatan }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold {{ $sisaHari <= 7 ? 'text-red-600' : 'text-orange-500' }}">Sisa {{ $sisaHari }} Hari</p>
                            <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($izin->tanggal_akhir)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 flex flex-col items-center">
                        <svg class="w-10 h-10 mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm">Semua perizinan aman, tidak ada yang mendekati masa tenggang.</p>
                    </div>
                @endforelse
            </div>
        </x-card>

        <!-- Panel 2: Tugas Rapat/BAR Pending -->
        <x-card class="!p-0 overflow-visible shadow-sm border border-amber-100 flex flex-col h-full">
            <div class="px-5 py-4 border-b border-gray-100 bg-amber-50/50 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="font-bold text-gray-900">Rapat & BAR Dalam Proses</h3>
                </div>
            </div>

            <div class="p-0 flex-1">
                @forelse($alertRapat as $rapat)
                    <div class="px-5 py-3 border-b border-gray-50 hover:bg-gray-50 flex items-center justify-between transition-colors last:border-0">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 line-clamp-1" title="{{ $rapat->tentang }}">{{ $rapat->tentang }}</p>
                            <p class="text-xs text-gray-500">Tgl: {{ \Carbon\Carbon::parse($rapat->tanggal_rapat)->format('d/m/Y') }}</p>
                        </div>
                        <span class="px-2.5 py-1 text-[10px] font-bold text-amber-700 bg-amber-100 rounded-md shrink-0 border border-amber-200">
                            {{ $rapat->status }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 flex flex-col items-center">
                        <svg class="w-10 h-10 mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <p class="text-sm">Luar biasa! Semua data rapat & BAR telah diselesaikan.</p>
                    </div>
                @endforelse
            </div>
        </x-card>

    </div>

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const palette = ['#1E3A8A', '#3B82F6', '#F97316', '#FDE047', '#22C55E', '#F87171', '#A855F7'];

    // Kearsipan bar chart
    new Chart(document.getElementById('chartKearsipan'), {
        type: 'bar',
        data: {
            labels: ['PA Tekstual', 'PA Non Tekstual', 'PA Teknik', 'DOF'],
            datasets: [{
                data: [
                    {{ $kearsipanStats['paTekstual'] }},
                    {{ $kearsipanStats['paNonTekstual'] }},
                    {{ $kearsipanStats['paTeknik'] }},
                    {{ $kearsipanStats['dof'] }}
                ],
                backgroundColor: palette,
                borderRadius: 6,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Administrasi bar chart
    new Chart(document.getElementById('chartAdministrasi'), {
        type: 'bar',
        data: {
            labels: ['Pemeliharaan', 'Pengiriman', 'Kurir', 'Fotocopy', 'Undangan', 'Surat', 'BAR SK Memo'],
            datasets: [{
                data: [
                    {{ $administrasiStats['pemeliharaan'] }},
                    {{ $administrasiStats['pengiriman'] }},
                    {{ $administrasiStats['kurir'] }},
                    {{ $administrasiStats['fotocopy'] }},
                    {{ $administrasiStats['undangan'] }},
                    {{ $administrasiStats['surat'] }},
                    {{ $administrasiStats['barSkMemo'] }}
                ],
                backgroundColor: palette,
                borderRadius: 6,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Karyawan donut
    new Chart(document.getElementById('chartKaryawan'), {
        type: 'doughnut',
        data: {
            labels: ['Organik', 'Non Organik'],
            datasets: [{
                data: [{{ $karyawanStats['organik'] }}, {{ $karyawanStats['nonOrganik'] }}],
                backgroundColor: ['#1E3A8A', '#F97316'],
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Pensiun bar
    new Chart(document.getElementById('chartPensiun'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($karyawanStats['pensiun'])),
            datasets: [{
                data: @json(array_values($karyawanStats['pensiun'])),
                backgroundColor: ['#F87171', '#F97316', '#FDE047', '#22C55E'],
                borderRadius: 6,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Perizinan per tahun
    new Chart(document.getElementById('chartPerizinanTahun'), {
        type: 'line',
        data: {
            labels: @json($perizinanStats['statistikSemuaTahun']->pluck('tahun')),
            datasets: [{
                label: 'Total Terbit',
                data: @json($perizinanStats['statistikSemuaTahun']->map(function($r) {
                    return $r->produk + $r->aset + $r->proyek + $r->peralatan_pabrik + $r->adm;
                })),
                borderColor: '#1E3A8A',
                backgroundColor: 'rgba(30,58,138,0.1)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
});
</script>
@endsection