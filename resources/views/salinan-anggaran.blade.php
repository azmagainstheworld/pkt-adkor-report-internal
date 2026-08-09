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
                <span class="text-blue-600 font-medium">Salinan Anggaran</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Salinan Anggaran</h2>
            <p class="text-sm text-gray-500">Arsip dan rekapitulasi data salinan anggaran perusahaan</p>
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
                <option value="juli" selected>Juli</option>
                <option value="juni">Juni</option>
                <option value="mei">Mei</option>
            </select>
        </div>
    </div>

    <!-- ================= TABEL UTAMA ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Rekapitulasi Salinan Anggaran</h3>
            <p class="text-xs text-gray-400">Daftar ringkasan salinan anggaran bulan berjalan</p>
        </div>

        <x-table :headers="['Tahun', 'Bulan', 'Detail', 'Anggaran RKAP', 'Komitmen', 'Realisasi', 'Real + Komit', '% R+K', 'Sisa Anggaran', '% Sisa', 'Link Detail']">
            <!-- Baris 1 -->
            <tr class="hover:bg-gray-50 transition-colors text-sm">
                <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                <td class="px-6 py-4 text-gray-600">Juli</td>
                <td class="px-6 py-4 font-medium text-gray-900">Biaya Pemeliharaan IT</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 100.000.000</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 10.000.000</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 40.000.000</td>
                <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 50.000.000</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700">50.0%</span></td>
                <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 50.000.000</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700">50.0%</span></td>
                <td class="px-6 py-4">
                    <button class="text-blue-600 hover:text-blue-800 font-semibold text-xs inline-flex items-center gap-1 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 transition-colors">
                        Lihat Rincian &rarr;
                    </button>
                </td>
            </tr>

            <!-- Baris 2 -->
            <tr class="bg-gray-50/60 hover:bg-gray-100 transition-colors text-sm">
                <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                <td class="px-6 py-4 text-gray-600">Juli</td>
                <td class="px-6 py-4 font-medium text-gray-900">Konsumsi Rapat Internal</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 15.000.000</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 1.000.000</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 13.500.000</td>
                <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 14.500.000</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700">96.6%</span></td>
                <td class="px-6 py-4 text-red-600 font-bold font-mono">Rp 500.000</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700">3.4%</span></td>
                <td class="px-6 py-4">
                    <button class="text-blue-600 hover:text-blue-800 font-semibold text-xs inline-flex items-center gap-1 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 transition-colors">
                        Lihat Rincian &rarr;
                    </button>
                </td>
            </tr>

            <!-- Baris 3 -->
            <tr class="hover:bg-gray-50 transition-colors text-sm">
                <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                <td class="px-6 py-4 text-gray-600">Juli</td>
                <td class="px-6 py-4 font-medium text-gray-900">Perjalanan Dinas Karyawan</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 50.000.000</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 5.000.000</td>
                <td class="px-6 py-4 text-gray-600 font-mono">Rp 35.000.000</td>
                <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 40.000.000</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-yellow-100 text-yellow-700">80.0%</span></td>
                <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 10.000.000</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-yellow-100 text-yellow-700">20.0%</span></td>
                <td class="px-6 py-4">
                    <button class="text-blue-600 hover:text-blue-800 font-semibold text-xs inline-flex items-center gap-1 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 transition-colors">
                        Lihat Rincian &rarr;
                    </button>
                </td>
            </tr>
        </x-table>
    </x-card>

    <!-- ================= BAGIAN KEDUA: TAB & TABEL RINCIAN ================= -->
    <div class="mb-8">
        <!-- 3 Tab Horizontal -->
        <div class="flex border-b border-gray-200 mb-6 gap-8">
            <button class="pb-3 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
                Dikelola
            </button>
            <!-- Tab Rutin (Aktif dengan garis bawah oranye) -->
            <button class="pb-3 text-sm font-bold text-pkt-jingga border-b-2 border-pkt-jingga transition-colors">
                Rutin
            </button>
            <button class="pb-3 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
                Investasi
            </button>
        </div>

        <!-- Tabel Rincian Terfilter (Rutin) -->
        <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white">
            
            <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Rincian Salinan Anggaran — Kategori Rutin</h3>
                    <p class="text-xs text-gray-400">Data terfilter khusus untuk jenis anggaran rutin</p>
                </div>
                <span class="px-3 py-1 bg-orange-50 text-orange-600 border border-orange-200 rounded-lg text-xs font-semibold">
                    Filter Aktif: Rutin
                </span>
            </div>

            <x-table :headers="['Tahun', 'Bulan', 'Detail', 'Anggaran RKAP', 'Komitmen', 'Realisasi', 'Real + Komit', '% R+K', 'Sisa Anggaran', '% Sisa', 'Link Detail']">
                <!-- Baris Contoh Terfilter Rutin 1 -->
                <tr class="hover:bg-gray-50 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                    <td class="px-6 py-4 text-gray-600">Juli</td>
                    <td class="px-6 py-4 font-medium text-gray-900">Biaya Pemeliharaan Gedung Kantor (Rutin)</td>
                    <td class="px-6 py-4 text-gray-600 font-mono">Rp 75.000.000</td>
                    <td class="px-6 py-4 text-gray-600 font-mono">Rp 5.000.000</td>
                    <td class="px-6 py-4 text-gray-600 font-mono">Rp 50.000.000</td>
                    <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 55.000.000</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700">73.3%</span></td>
                    <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 20.000.000</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700">26.7%</span></td>
                    <td class="px-6 py-4">
                        <button class="text-blue-600 hover:text-blue-800 font-semibold text-xs inline-flex items-center gap-1 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 transition-colors">
                            Lihat Rincian &rarr;
                        </button>
                    </td>
                </tr>

                <!-- Baris Contoh Terfilter Rutin 2 -->
                <tr class="bg-gray-50/60 hover:bg-gray-100 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-700 font-medium">2026</td>
                    <td class="px-6 py-4 text-gray-600">Juli</td>
                    <td class="px-6 py-4 font-medium text-gray-900">Langganan Internet & Utilitas (Rutin)</td>
                    <td class="px-6 py-4 text-gray-600 font-mono">Rp 30.000.000</td>
                    <td class="px-6 py-4 text-gray-600 font-mono">Rp 0</td>
                    <td class="px-6 py-4 text-gray-600 font-mono">Rp 28.000.000</td>
                    <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 28.000.000</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-yellow-100 text-yellow-700">93.3%</span></td>
                    <td class="px-6 py-4 text-gray-900 font-bold font-mono">Rp 2.000.000</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-md text-xs font-bold bg-yellow-100 text-yellow-700">6.7%</span></td>
                    <td class="px-6 py-4">
                        <button class="text-blue-600 hover:text-blue-800 font-semibold text-xs inline-flex items-center gap-1 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 transition-colors">
                            Lihat Rincian &rarr;
                        </button>
                    </td>
                </tr>
            </x-table>
        </x-card>
    </div>

</main>
@endsection