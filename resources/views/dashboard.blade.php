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
            <!-- <div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Ringkasan Bulan Ini</h3>
                <p class="text-sm text-gray-500">Ringkasan Bulan Ini</p>
            </div> -->
            <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                Lihat Semua Laporan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-5">
            
            <!-- Card 1: Karyawan -->
            <x-card class="p-5 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs font-bold rounded-full">+5%</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Karyawan</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">21</p>
                    <p class="text-[11px] text-gray-400">Organik: 6 | Non Organik: 15</p>
                </div>
            </x-card>

            <!-- Card 2: Ketidakhadiran -->
            <x-card class="p-5 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs font-bold rounded-full">-2%</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Ketidakhadiran</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">8</p>
                    <p class="text-[11px] text-gray-400">Total hari bulan berjalan</p>
                </div>
            </x-card>

            <!-- Card 3: Anggaran (Blue Card) -->
            <x-card class="!bg-pkt-biru !border-blue-800 shadow-md p-5 flex flex-col justify-between relative overflow-hidden h-full">
                <!-- Background Decoration Circle -->
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/5 rounded-full"></div>
                
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white">
                        <span class="text-lg font-semibold">$</span>
                    </div>
                    <span class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">+3%</span>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-blue-100 mb-1">Anggaran</p>
                    <p class="text-3xl font-bold text-white mb-2">74%</p>
                    <!-- Progress Bar -->
                    <div class="w-full bg-black/20 rounded-full h-1.5 mb-2">
                        <div class="bg-green-400 h-1.5 rounded-full" style="width: 74%"></div>
                    </div>
                    <p class="text-[11px] text-blue-200">Realisasi anggaran bulan ini</p>
                </div>
            </x-card>

            <!-- Card 4: Perizinan Perkantoran -->
            <x-card class="p-5 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs font-bold rounded-full">+1</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Perizinan Perkantoran</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">12</p>
                    <p class="text-[11px] text-gray-400">Perizinan terbit bulan ini</p>
                </div>
            </x-card>

            <!-- Card 5: Pelaporan -->
            <x-card class="p-5 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs font-bold rounded-full">+8%</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Pelaporan</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">34</p>
                    <p class="text-[11px] text-gray-400">Eksternal: 14 | Internal: 20</p>
                </div>
            </x-card>
            
            <!-- Card 6: Kearsipan (Blue Card) -->
            <x-card class="!bg-pkt-biru !border-blue-800 shadow-md p-5 flex flex-col justify-between relative overflow-hidden h-full">
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/5 rounded-full"></div>
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <span class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">+12</span>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-blue-100 mb-1">Kearsipan</p>
                    <p class="text-3xl font-bold text-white mb-2">156</p>
                    <p class="text-[11px] text-blue-200">Rekap total kearsipan</p>
                </div>
            </x-card>

            <!-- Card 7: Administrasi -->
            <x-card class="p-5 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs font-bold rounded-full">+6%</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Administrasi</p>
                    <p class="text-3xl font-bold text-gray-900 mb-2">48</p>
                    <p class="text-[11px] text-gray-400">Total aktivitas bulan ini</p>
                </div>
            </x-card>

        </div>
    </div>

    <!-- Section: Akses Cepat -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 mb-4">Akses Cepat</h3>
        <div class="flex flex-wrap gap-4">
            <x-button variant="outline" class="!text-blue-600 !border-gray-200 hover:!bg-blue-50 hover:!border-blue-300 px-5 py-2.5">
                Buat Laporan Baru
            </x-button>
            
            <x-button variant="outline" class="!text-blue-600 !border-gray-200 hover:!bg-blue-50 hover:!border-blue-300 px-5 py-2.5">
                Unggah Dokumen
            </x-button>
            
            <x-button variant="outline" class="!text-blue-600 !border-gray-200 hover:!bg-blue-50 hover:!border-blue-300 px-5 py-2.5">
                Ajukan Perizinan
            </x-button>
            
            <x-button variant="outline" class="!text-blue-600 !border-gray-200 hover:!bg-blue-50 hover:!border-blue-300 px-5 py-2.5">
                Input Ketidakhadiran
            </x-button>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- SECTION: PANEL ALERT & PEKERJAAN TERTUNDA -->
    <!-- ======================================================= -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Panel 1: Perizinan Akan Kedaluwarsa -->
        <x-card class="!p-0 overflow-hidden shadow-sm border border-red-100 flex flex-col h-full">
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
        <x-card class="!p-0 overflow-hidden shadow-sm border border-amber-100 flex flex-col h-full">
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