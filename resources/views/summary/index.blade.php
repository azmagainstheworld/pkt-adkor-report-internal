@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Summary</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Realisasi Kegiatan Rutin/Strategis</h2>
            <p class="text-sm text-gray-500">Rekapitulasi Kegiatan Rutin/Strategis Departemen Administrasi Korporat, digambarkan dalam tabel sebagai berikut.</p>
        </div>

        <!-- Filter Tahun -->
        <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-3">
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors hover:border-gray-300 hover:bg-gray-50">
                <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}" {{ (string) $selectedYear === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= TABEL SUMMARY ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white">

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-pkt-jingga">
                        <th class="px-4 py-4 text-xs font-bold text-white text-center whitespace-nowrap sticky left-0 bg-pkt-jingga z-10">No</th>
                        <th class="px-4 py-4 text-xs font-bold text-white text-center whitespace-nowrap">Tahun</th>
                        <th class="px-4 py-4 text-xs font-bold text-white text-center whitespace-nowrap">Bulan</th>
                        @foreach ($metrics as $def)
                            <th class="px-4 py-4 text-xs font-bold text-white text-center whitespace-nowrap min-w-[140px]">{{ $def['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($tableRows as $row)
                        <tr class="{{ $loop->even ? 'bg-gray-50/50' : '' }} hover:bg-blue-50/40 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-400 font-mono text-xs sticky left-0 bg-inherit">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-center text-gray-700 font-semibold whitespace-nowrap">{{ $row['tahun'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-700 whitespace-nowrap">{{ $row['bulan'] }}</td>

                            @foreach ($metrics as $key => $def)
                                @php
                                    $value = $row['values'][$key];
                                    $percent = $value > 0 ? max(6, round($value / $maxPerColumn[$key] * 100)) : 0;
                                @endphp
                                <td class="px-4 py-3 align-middle">
                                    <div class="flex flex-col items-center gap-1 min-w-[110px]">
                                        <span class="text-xs font-semibold text-gray-700 font-mono">{{ $value > 0 ? number_format($value, 0, ',', '.') : '-' }}</span>
                                        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            @if ($value > 0)
                                                <div class="h-full rounded-full" style="width: {{ $percent }}%; background-color: {{ $def['color'] }};"></div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($metrics) + 3 }}" class="px-6 py-10 text-center text-gray-500 text-sm">
                                Belum ada data untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($tableRows->isNotEmpty())
                    <tfoot>
                        <tr class="bg-gray-50 border-t-2 border-gray-200 font-bold">
                            <td class="px-4 py-4 sticky left-0 bg-gray-50"></td>
                            <td colspan="2" class="px-4 py-4 text-right text-gray-800 whitespace-nowrap">Total Keseluruhan</td>
                            @foreach ($metrics as $key => $def)
                                <td class="px-4 py-4 text-center text-gray-900 font-mono">{{ number_format($totals[$key], 0, ',', '.') }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

    </x-card>

    <p class="text-xs text-gray-400 mt-4">
        Data bersifat read-only, diambil otomatis dari modul Perizinan, Pelaporan, BA/SK/Memo, Pengiriman Dokumen, Undangan, DOF, Arsip, dan Teknikal File.
    </p>

</main>
@endsection