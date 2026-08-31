@extends('layouts.app')

@section('content')
<style>th { white-space: nowrap !important; }</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 min-w-0 min-h-0 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Kearsipan</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Rekap</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Rekapitulasi Kearsipan</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('rekap.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 outline-none focus:border-orange-500">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 outline-none focus:border-orange-500">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- CHART DINAMIS -->
    <div class="mb-8 h-[450px]">
        @php
            $chartTitle = $filterTahun == 'semua' ? "Statistik Rekapitulasi (Semua Tahun)" : "Statistik Rekapitulasi per Bulan ($filterTahun)";
            $chartSubtitle = "Data diakumulasi otomatis dari modul PA dan DOF";
        @endphp
        <x-dynamic-chart title="{{ $chartTitle }}" subtitle="{{ $chartSubtitle }}" type="bar" id="rekapChart" />
    </div>

    <!-- TABEL REKAP OTOMATIS -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-between items-center gap-3">
            <h3 class="font-bold text-gray-800 text-sm ml-2">Tabel Rekapitulasi</h3>
            <span class="text-xs text-orange-600 font-medium italic hidden sm:inline-block">Data pada tabel ini tersinkronisasi otomatis dengan menu lain.</span>
        </div>
        <div class="overflow-x-auto w-full max-w-full">
            @php
                // Menyusun Header Dinamis
                $tableHeaders = ['Tahun', 'Bulan'];
                foreach($headers as $h) { $tableHeaders[] = $h; }
            @endphp
            
            <x-table :headers="$tableHeaders">
                @forelse($dataTable as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        
                        <!-- Loop nilai berdasarkan nama kolom -->
                        @foreach($headers as $namaKolom)
                            <td class="px-4 py-3 text-blue-700 font-medium text-center bg-blue-50/10">{{ number_format($row['kolom'][$namaKolom], 0, ',', '.') }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($tableHeaders) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong. Silakan isi data di modul PA atau DOF terlebih dahulu.</td></tr>
                @endforelse
                
                @if(count($dataTable) > 0)
                <tr class="bg-gray-100 font-bold text-xs whitespace-nowrap border-t-2 border-gray-300">
                    <td colspan="2" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>
                    @foreach($headers as $namaKolom)
                        <td class="px-4 py-4 text-orange-700 text-center bg-orange-50/50">{{ number_format($totalsKolom[$namaKolom], 0, ',', '.') }}</td>
                    @endforeach
                </tr>
                @endif
            </x-table>
        </div>
    </x-card>
</main>

<script>
    // Inisialisasi ChartJS
    const rawChartData = {!! json_encode($chartJsonData) !!};
    let chartInstance = null;

    function initChart(chartId, chartType) {
        const ctx = document.getElementById('canvas_' + chartId);
        if (!ctx) return;
        if (chartInstance) chartInstance.destroy();

        chartInstance = new Chart(ctx, {
            type: chartType,
            data: { labels: rawChartData.labels, datasets: rawChartData.datasets },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: true, display: (chartType === 'bar' || chartType === 'line') },
                    x: { display: (chartType === 'bar' || chartType === 'line') }
                }
            }
        });
    }

    function changeChartType(chartId, newType) { initChart(chartId, newType); }
    document.addEventListener("DOMContentLoaded", function() { initChart('rekapChart', 'bar'); });
</script>
@endsection
