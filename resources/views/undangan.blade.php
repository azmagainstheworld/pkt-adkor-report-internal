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
                <span class="text-blue-600 font-medium">Undangan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Undangan</h2>
            <p class="text-sm text-gray-500 font-medium">{{ \Carbon\Carbon::now()->locale('en')->translatedFormat('l, d F Y') }}</p>
        </div>
        
        <!-- Filter Kanan Atas -->
        <form action="{{ route('undangan.index') }}" method="GET" class="flex items-center gap-3">
            
            <!-- Dropdown Tahun Dinamis dengan Opsi Semua Tahun -->
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="semua" {{ $tahunFilter == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $tahunFilter == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
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

    <!-- ================= CHART SECTION ================= -->
    <x-card class="!rounded-xl overflow-hidden shadow-sm border border-gray-100 mb-8 p-6 bg-white">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Statistik Distribusi Undangan per Bulan</h3>
                <p class="text-xs text-gray-400">
                    Perbandingan volume undangan intern dan ekstern 
                    {{ $tahunFilter == 'semua' ? 'Keseluruhan (Semua Tahun)' : 'Tahun ' . $tahunFilter }}
                </p>
            </div>
            
            <!-- Legenda Warna -->
            <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#0056A3]"></span><span class="text-gray-700 font-medium">Undangan Intern</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#F7941E]"></span><span class="text-gray-700 font-medium">Undangan Ekstern</span></div>
            </div>
        </div>

        <!-- Canvas untuk Chart.js -->
        <div class="h-64 w-full relative">
            <canvas id="undanganChart"></canvas>
        </div>
    </x-card>

    <!-- ================= TABEL REKAPITULASI UNDANGAN ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Rekapitulasi Undangan Intern & Ekstern</h3>
                <p class="text-xs text-gray-400">Rincian jumlah undangan per periode bulanan</p>
            </div>
            
            <x-button variant="primary" onclick="bukaModalTambah()" class="!bg-[#F7941E] hover:!bg-orange-600 border-none !rounded-xl !py-2 shadow-sm text-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Data
            </x-button>
        </div>

        <x-table :headers="['Tahun', 'Bulan', 'Undangan Intern', 'Undangan Ekstern']">
            @forelse($tableData as $index => $row)
                <tr class="hover:bg-gray-50 transition-colors text-sm {{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }}">
                    <td class="px-6 py-4 text-gray-700 font-medium">{{ $row->tahun }}</td>
                    <td class="px-6 py-4 text-gray-900 font-medium">{{ $row->bulan }}</td>
                    <td class="px-6 py-4 text-gray-700 font-semibold">{{ $row->undangan_intern }} Berkas</td>
                    <td class="px-6 py-4 text-gray-700 font-semibold">{{ $row->undangan_ekstern }} Berkas</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">Tidak ada data untuk filter yang dipilih.</td>
                </tr>
            @endforelse

            <!-- Baris Grand Total -->
            @if(count($tableData) > 0)
            <tr class="bg-gray-100 font-bold text-gray-900 text-sm border-t border-gray-200">
                <td class="px-6 py-4" colspan="2">Grand Total</td>
                <td class="px-6 py-4">{{ $totalIntern }} Berkas</td>
                <td class="px-6 py-4">{{ $totalEkstern }} Berkas</td>
            </tr>
            @endif
        </x-table>
    </x-card>

</main>

<!-- ================= MODAL TAMBAH / UPDATE UNDANGAN ================= -->
<x-modal id="modalUndangan" title="Input Data Undangan" description="Pilih jenis undangan dan masukkan jumlahnya">
    <form action="{{ route('undangan.store') }}" method="POST" id="formUndangan" class="space-y-5" novalidate>
        @csrf
        
        <!-- Input Periode (Bulan & Tahun disatukan dalam 1 Field) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
            <input type="month" name="periode" required value="{{ date('Y-m') }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
            <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
        </div>

        <hr class="border-gray-200 my-2">

        <!-- Dropdown Jenis Undangan -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Undangan <span class="text-red-500">*</span></label>
            <select name="jenis_undangan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="" disabled selected>Pilih Jenis Undangan...</option>
                <option value="intern">Undangan Intern</option>
                <option value="ekstern">Undangan Ekstern</option>
            </select>
            <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Jenis undangan wajib dipilih!</span>
        </div>

        <!-- Input Jumlah Undangan -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Undangan <span class="text-red-500">*</span></label>
            <input type="number" name="jumlah_undangan" min="0" required placeholder="Contoh: 45" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm transition-colors">
            <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Jumlah undangan wajib diisi!</span>
        </div>
        
    </form>

    <x-slot name="footer">
        <x-button variant="outline" onclick="closeModalTambah()" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
        <x-button variant="primary" type="submit" form="formUndangan" class="!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600">Simpan Data</x-button>
    </x-slot>
</x-modal>

<!-- ================= JAVASCRIPT ================= -->
<script>
    // ----------------------------------------------------
    // LOGIKA BUKA TUTUP MODAL
    // ----------------------------------------------------
    function bukaModalTambah() {
        const modal = document.getElementById('modalUndangan');
        if (modal) modal.classList.remove('hidden');
    }

    function closeModalTambah() {
        const modal = document.getElementById('modalUndangan');
        if (modal) modal.classList.add('hidden');
    }

    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            bukaModalTambah();
        });
    @endif

    // ----------------------------------------------------
    // LOGIKA VALIDASI CLIENT-SIDE (Cek form kosong)
    // ----------------------------------------------------
    const formUndangan = document.getElementById('formUndangan');
    if (formUndangan) {
        formUndangan.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = formUndangan.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                const errorSpan = field.nextElementSibling;
                
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500', 'bg-red-50'); 
                    field.classList.remove('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) {
                        errorSpan.classList.remove('hidden'); 
                    }
                } else {
                    field.classList.remove('border-red-500', 'bg-red-50');
                    field.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) {
                        errorSpan.classList.add('hidden'); 
                    }
                }
            });

            if (!isValid) {
                e.preventDefault(); 
            }
        });

        const requiredFields = formUndangan.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    this.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) {
                        errorSpan.classList.add('hidden');
                    }
                }
            });
        });
    }

    // ----------------------------------------------------
    // LOGIKA CHART.JS DINAMIS
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        const canvasUndangan = document.getElementById('undanganChart');
        
        if (canvasUndangan) {
            const ctx = canvasUndangan.getContext('2d');
            
            const rawChartData = {!! json_encode($chartData) !!};

            const labels = rawChartData.map(d => d.label);
            const dataIntern = rawChartData.map(d => d.intern);
            const dataEkstern = rawChartData.map(d => d.ekstern);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Undangan Intern',
                            data: dataIntern,
                            backgroundColor: '#0056A3', // Biru PKT
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Undangan Ekstern',
                            data: dataEkstern,
                            backgroundColor: '#F7941E', // Oranye PKT
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false }, 
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
                            ticks: { font: { family: "'Poppins', sans-serif", size: 11 } }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: { font: { family: "'Poppins', sans-serif", size: 11 } }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection