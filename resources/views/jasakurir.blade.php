@extends('layouts.app')

@section('content')
{{-- Load library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <x-success-modal />

    {{-- Notifikasi Error Validasi Backend --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm flex flex-col shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-bold">Gagal memproses data. Periksa inputan Anda:</span>
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
                <span class="text-gray-500">Administrasi</span>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Jasa Kurir</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Jasa Kurir</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
        
        <!-- Filter Kanan Atas (Menggunakan Form GET) -->
        <form action="{{ route('jasakurir') }}" method="GET" class="flex items-center gap-3">
            <!-- Dropdown Tahun -->
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer">
                @for($i = date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ $tahunFilter == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>

            <!-- Dropdown Bulan -->
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer">
                <option value="semua" {{ $bulanFilter == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $bulanFilter == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART SECTION (Chart.js Dinamis) ================= -->
    <x-card class="!rounded-xl overflow-hidden shadow-sm border border-gray-100 mb-8 p-6 bg-white">
        <div class="mb-4">
            <h3 class="font-bold text-gray-900 text-lg">Statistik Penggunaan Jasa Kurir</h3>
            <p class="text-xs text-gray-400">Total volume pengiriman dokumen/barang per ekspedisi ({{ $tahunFilter }})</p>
        </div>

        <!-- Canvas untuk Chart.js -->
        <div class="h-80 w-full relative">
            <canvas id="kurirChart"></canvas>
        </div>
    </x-card>

    <!-- ================= DATA TABLE SECTION ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white">
        
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-wrap gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Detail Pengiriman per Ekspedisi</h3>
                <p class="text-xs text-gray-400">Data rincian jumlah layanan yang digunakan</p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Tombol Tambah Kurir Baru -->
                <x-button variant="outline" onclick="bukaModalTambahKurir()" class="!rounded-xl !py-2 shadow-sm text-xs font-medium text-gray-600 border-gray-300 hover:bg-gray-50">
                    + Jasa Ekspedisi Baru
                </x-button>

                <!-- Tombol Tambah Data Bulan Ini -->
                <x-button variant="primary" onclick="bukaModalTambahData()" class="!bg-[#F7941E] hover:!bg-orange-600 border-none !rounded-xl !py-2 shadow-sm text-xs">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Data
                </x-button>
            </div>
        </div>

        {{-- Menyiapkan Header Dinamis dari Database --}}
        @php
            $headers = ['Tahun', 'Bulan'];
            foreach($kurirMaster as $kurir) {
                $headers[] = strtoupper($kurir->nama_kurir);
            }
            $headers[] = 'Total';
        @endphp

        <div class="overflow-x-auto">
            <x-table :headers="$headers">
                @forelse($tableData as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-100 last:border-0">
                        <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">{{ $row['tahun'] }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium whitespace-nowrap">{{ $row['bulan'] }}</td>
                        
                        {{-- Loop data per kurir --}}
                        @foreach($kurirMaster as $kurir)
                            <td class="px-6 py-4 text-gray-600 text-center">{{ $row['kurir_'.$kurir->id] ?? 0 }}</td>
                        @endforeach
                        
                        <td class="px-6 py-4 font-bold text-blue-900 bg-blue-50 text-center">{{ $row['total_semua'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-gray-500">Belum ada data pengiriman untuk filter ini.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

</main>


<!-- ================= MODAL TAMBAH DATA BULANAN ================= -->
<x-modal id="modalTambahData" title="Input Data Pengiriman" description="Pilih ekspedisi dan masukkan jumlah dokumen/barang">
    <form action="{{ route('jasakurir.data.store') }}" method="POST" id="formDataKurir" class="space-y-5">
        @csrf
        
        <div class="grid grid-cols-2 gap-4">
            <!-- Tahun -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                <input type="number" name="tahun" value="{{ date('Y') }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none">
            </div>
            
            <!-- Bulan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan <span class="text-red-500">*</span></label>
                <select name="bulan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled>Pilih Bulan...</option>
                    @php
                        $bulanSekarang = \Carbon\Carbon::now()->translatedFormat('F');
                    @endphp
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                        <option value="{{ $b }}" {{ $bulanSekarang == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr class="border-gray-200 my-2">
        
        <!-- Dropdown Jasa Pengiriman -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jasa Pengiriman (Ekspedisi) <span class="text-red-500">*</span></label>
            <select name="jasa_kurir_id" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                <option value="" disabled selected>Pilih Jasa Pengiriman...</option>
                @foreach($kurirMaster as $kurir)
                    <option value="{{ $kurir->id }}">{{ strtoupper($kurir->nama_kurir) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Input Jumlah -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Pengiriman <span class="text-red-500">*</span></label>
            <input type="number" name="jumlah" min="0" required placeholder="Contoh: 150" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
        </div>
        
    </form>

    <x-slot name="footer">
        <x-button variant="outline" onclick="closeModalTambahData()" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
        <x-button variant="primary" type="submit" form="formDataKurir" class="!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600">Simpan Data</x-button>
    </x-slot>
</x-modal>

<!-- ================= MODAL TAMBAH MASTER KURIR BARU ================= -->
<x-modal id="modalMasterKurir" title="Tambah Ekspedisi Baru" description="Daftarkan nama jasa kurir baru ke dalam sistem">
    <form action="{{ route('jasakurir.master.store') }}" method="POST" id="formMasterKurir" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Jasa Kurir / Ekspedisi <span class="text-red-500">*</span></label>
            <input type="text" name="nama_kurir" required placeholder="Contoh: Ninja Xpress..." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
        </div>
    </form>

    <x-slot name="footer">
        <x-button variant="outline" onclick="closeModalTambahKurir()" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
        <x-button variant="primary" type="submit" form="formMasterKurir" class="!px-6 !py-2.5 !rounded-lg !bg-blue-700 hover:!bg-blue-800">Simpan Ekspedisi</x-button>
    </x-slot>
</x-modal>


<!-- ================= JAVASCRIPT ================= -->
<script>
    // ----------------------------------------------------
    // LOGIKA MODAL
    // ----------------------------------------------------
    function bukaModalTambahData() {
        const modal = document.getElementById('modalTambahData');
        if (modal) modal.classList.remove('hidden');
    }

    function closeModalTambahData() {
        const modal = document.getElementById('modalTambahData');
        if (modal) modal.classList.add('hidden');
    }

    function bukaModalTambahKurir() {
        const modal = document.getElementById('modalMasterKurir');
        if (modal) modal.classList.remove('hidden');
    }

    function closeModalTambahKurir() {
        const modal = document.getElementById('modalMasterKurir');
        if (modal) modal.classList.add('hidden');
    }

    // ----------------------------------------------------
    // LOGIKA CHART.JS (Dinamis dari Database)
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        const canvasKurir = document.getElementById('kurirChart');
        
        if (canvasKurir) {
            const ctx = canvasKurir.getContext('2d');
            
            // Ambil data yang dilempar dari Controller via Blade
            const rawKurirMaster = {!! json_encode($kurirMaster) !!};
            const rawChartData = {!! json_encode($chartData) !!};

            // Array warna hex yang bisa digunakan untuk kurir
            const colorPalette = [
                '#0056A3', '#F7941E', '#EF4444', '#EAB308', 
                '#EC4899', '#9333EA', '#10B981', '#14B8A6',
                '#F97316', '#6366F1', '#8B5CF6', '#06B6D4',
                '#84CC16', '#3B82F6', '#F43F5E', '#D946EF',
                '#8B5CF6', '#A855F7'
            ];

            // Setup Labels (Bulan)
            const labels = rawChartData.map(d => d.bulan.substring(0, 3)); // Jan, Feb, Mar

            // Setup Datasets (Dinamis sesuai jumlah kurir master)
            const datasets = rawKurirMaster.map((kurir, index) => {
                return {
                    label: kurir.nama_kurir,
                    data: rawChartData.map(d => d[kurir.nama_kurir] || 0),
                    backgroundColor: colorPalette[index % colorPalette.length],
                    borderRadius: 4,
                    borderSkipped: false,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                };
            });

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleFont: { size: 13, family: "'Poppins', sans-serif" },
                            bodyFont: { size: 12, family: "'Poppins', sans-serif" },
                            padding: 12,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#F3F4F6',
                                drawBorder: false,
                            },
                            ticks: {
                                font: { family: "'Poppins', sans-serif", size: 11 }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                font: { family: "'Poppins', sans-serif", size: 11 }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection