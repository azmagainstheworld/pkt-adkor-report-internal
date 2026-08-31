@extends('layouts.app')

@section('content')
<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative">
    
    <!-- Welcome Title -->
    <h2 class="text-3xl font-bold text-gray-900 mb-1">Selamat datang, {{ auth()->user()->name }}</h2>
    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>

    <!-- Section: Ringkasan Bulan Ini -->
    <div class="mb-8">
        <div class="flex items-end justify-end mb-4">
            <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                Lihat Semua Laporan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-5">
            
            <!-- Card 1: Karyawan -->
            <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 group-hover:bg-blue-50 transition-colors flex items-center justify-center text-gray-400 group-hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gray-700 transition-colors">Karyawan</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">{{ $totalKaryawan }}</p>
                    <p class="text-[11px] text-gray-400">Organik: {{ $karyawanOrganik }} | Non Organik: {{ $karyawanNonOrganik }}</p>
                </div>
            </x-card>

            <!-- Card 2: Ketidakhadiran -->
            <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 group-hover:bg-blue-50 transition-colors flex items-center justify-center text-gray-400 group-hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gray-700 transition-colors">Ketidakhadiran</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">{{ $totalKetidakhadiran }}</p>
                    <p class="text-[11px] text-gray-400">Total hari bulan berjalan</p>
                </div>
            </x-card>

            <!-- Card 3: Anggaran -->
            <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 group-hover:bg-blue-50 transition-colors flex items-center justify-center text-gray-400 group-hover:text-blue-600">
                        <span class="text-lg font-semibold">$</span>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gray-700 transition-colors">Anggaran</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">{{ $persenAnggaran }}%</p>
                    <!-- Progress Bar Dinamis -->
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2 overflow-visible">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $persenAnggaran }}%"></div>
                    </div>
                    <p class="text-[11px] text-gray-400">Realisasi anggaran bulan ini</p>
                </div>
            </x-card>

            <!-- Card 4: Perizinan Perkantoran -->
            <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 group-hover:bg-blue-50 transition-colors flex items-center justify-center text-gray-400 group-hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gray-700 transition-colors">Perizinan Perkantoran</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">{{ $perizinanBulanIni }}</p>
                    <p class="text-[11px] text-gray-400">Perizinan terbit bulan ini</p>
                </div>
            </x-card>

            <!-- Card 5: Pelaporan -->
            <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 group-hover:bg-blue-50 transition-colors flex items-center justify-center text-gray-400 group-hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gray-700 transition-colors">Pelaporan</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">{{ $totalPelaporan }}</p>
                    <p class="text-[11px] text-gray-400">Eksternal: {{ $pelaporanEksternal }} | Internal: {{ $pelaporanInternal }}</p>
                </div>
            </x-card>
            
            <!-- Card 6: Kearsipan -->
            <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 group-hover:bg-blue-50 transition-colors flex items-center justify-center text-gray-400 group-hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gray-700 transition-colors">Kearsipan</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">{{ $totalKearsipan }}</p>
                    <p class="text-[11px] text-gray-400">Rekap total kearsipan</p>
                </div>
            </x-card>

            <!-- Card 7: Administrasi -->
            <x-card class="p-5 flex flex-col justify-between h-full bg-white border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 group-hover:bg-blue-50 transition-colors flex items-center justify-center text-gray-400 group-hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gray-700 transition-colors">Administrasi</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">{{ $totalAdministrasi }}</p>
                    <p class="text-[11px] text-gray-400">Total aktivitas bulan ini</p>
                </div>
            </x-card>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- SECTION: PANEL ALERT & PEKERJAAN TERTUNDA -->
    <!-- ======================================================= -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        
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
@endsection
