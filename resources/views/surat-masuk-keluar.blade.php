@extends('layouts.app')

@section('content')
<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-500">Administrasi</span>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Surat Masuk & Keluar</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Surat</h2>
            <p class="text-sm text-gray-500">Rekapitulasi pencatatan surat masuk dan surat keluar perusahaan</p>
        </div>
        
        <!-- Filter Kanan Atas -->
        <div class="flex items-center gap-3">
            <!-- Dropdown Tahun -->
            <select class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                <option value="" disabled selected>Pilih Tahun</option>
                <option value="2026" selected>2026</option>
                <option value="2025">2025</option>
            </select>

            <!-- Dropdown Bulan -->
            <select class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                <option value="" disabled selected>Pilih Bulan</option>
                <option value="juli" selected>Juli 2026</option>
                <option value="juni">Juni 2026</option>
                <option value="mei">Mei 2026</option>
                <option value="semua">Semua Bulan</option>
            </select>
        </div>
    </div>

    <!-- ================= TABEL REKAPITULASI SURAT ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Rekapitulasi Surat Masuk & Keluar</h3>
                <p class="text-xs text-gray-400">Rincian jumlah volume persuratan per periode bulanan</p>
            </div>
            
            <x-button variant="primary" class="!bg-[#F7941E] hover:!bg-orange-600 border-none !rounded-xl !py-2 shadow-sm text-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Surat
            </x-button>
        </div>

        <x-table :headers="['Tahun', 'Bulan', 'Surat Masuk', 'Surat Keluar']">
            <!-- Baris 1 -->
            <tr class="hover:bg-gray-50 transition-colors text-sm">
                <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                <td class="px-6 py-4 text-gray-900 font-medium">Juli</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">120 Berkas</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">85 Berkas</td>
            </tr>

            <!-- Baris 2 -->
            <tr class="bg-gray-50/60 hover:bg-gray-100 transition-colors text-sm">
                <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                <td class="px-6 py-4 text-gray-900 font-medium">Juni</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">145 Berkas</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">92 Berkas</td>
            </tr>

            <!-- Baris 3 -->
            <tr class="hover:bg-gray-50 transition-colors text-sm">
                <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                <td class="px-6 py-4 text-gray-900 font-medium">Mei</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">110 Berkas</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">78 Berkas</td>
            </tr>

            <!-- Baris 4 -->
            <tr class="bg-gray-50/60 hover:bg-gray-100 transition-colors text-sm">
                <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                <td class="px-6 py-4 text-gray-900 font-medium">April</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">130 Berkas</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">90 Berkas</td>
            </tr>

            <!-- Baris Grand Total -->
            <tr class="bg-gray-100 font-bold text-gray-900 text-sm border-t border-gray-200">
                <td class="px-6 py-4" colspan="2">Grand Total</td>
                <td class="px-6 py-4">505 Berkas</td>
                <td class="px-6 py-4">345 Berkas</td>
            </tr>
        </x-table>
    </x-card>

    <!-- ================= CHART SECTION (Grouped Bar Chart 2 Kategori) ================= -->
    <x-card class="!rounded-xl overflow-hidden shadow-sm border border-gray-100 mb-8 p-6 bg-white">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Statistik Surat Masuk & Keluar per Bulan</h3>
                <p class="text-xs text-gray-400">Perbandingan volume surat masuk dan surat keluar tahun 2026</p>
            </div>
            
            <!-- Legenda Warna -->
            <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#0056A3]"></span><span class="text-gray-700 font-medium">Surat Masuk</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#F7941E]"></span><span class="text-gray-700 font-medium">Surat Keluar</span></div>
            </div>
        </div>

        <!-- Visualisasi Bar Chart (Mockup menggunakan Flexbox Tailwind) -->
        <div class="h-64 flex items-end justify-between gap-2 border-b border-gray-200 pb-2 relative pt-4">
            <!-- Garis bantu grid -->
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-2 border-l border-gray-200 pl-2 z-0">
                <div class="border-t border-gray-100 w-full h-0"></div>
                <div class="border-t border-gray-100 w-full h-0"></div>
                <div class="border-t border-gray-100 w-full h-0"></div>
                <div class="border-t border-gray-100 w-full h-0"></div>
                <div class="border-t border-gray-100 w-full h-0"></div>
            </div>

            <!-- Bar Januari - Desember -->
            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'] as $bulan)
                @php
                    $suratMasuk = rand(40, 90);
                    $suratKeluar = rand(30, 75);
                @endphp
                <div class="w-full flex flex-col items-center justify-end h-full relative z-10 group cursor-pointer">
                    
                    <!-- Tooltip Hover -->
                    <div class="absolute bottom-full mb-2 hidden group-hover:block w-36 bg-gray-900 text-white text-xs p-2.5 rounded-lg shadow-lg z-20 left-1/2 transform -translate-x-1/2 text-left">
                        <span class="font-bold block mb-1 border-b border-gray-700 pb-1">{{ $bulan }} 2026</span>
                        <div class="flex justify-between"><span>Masuk:</span> <span class="font-bold">{{ $suratMasuk }}</span></div>
                        <div class="flex justify-between"><span>Keluar:</span> <span class="font-bold">{{ $suratKeluar }}</span></div>
                    </div>
                    
                    <!-- Grouped Bars (2 Kategori Warna) -->
                    <div class="flex items-end gap-1 h-full w-full justify-center pb-1">
                        <div style="height: {{ $suratMasuk }}%" class="w-2.5 sm:w-3.5 bg-[#0056A3] rounded-t-sm hover:brightness-125 transition-all"></div>
                        <div style="height: {{ $suratKeluar }}%" class="w-2.5 sm:w-3.5 bg-[#F7941E] rounded-t-sm hover:brightness-125 transition-all"></div>
                    </div>
                    
                    <!-- Label Sumbu X -->
                    <span class="text-[10px] text-gray-500 text-center font-medium mt-1">{{ $bulan }}</span>
                </div>
            @endforeach
        </div>
    </x-card>

</main>
@endsection