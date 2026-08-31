@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <!-- ================= FILTER (baris sendiri, TIDAK bertabrakan dengan judul/deskripsi) ================= -->
    <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <select name="year" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm font-medium text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors hover:border-gray-300 hover:bg-gray-50">
            <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
            @foreach ($availableYears as $y)
                <option value="{{ $y }}" {{ (string) $selectedYear === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>

        <select name="month" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm font-medium text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors hover:border-gray-300 hover:bg-gray-50">
            <option value="all" {{ $selectedMonth === 'all' ? 'selected' : '' }}>Semua Bulan</option>
            @foreach ($bulanIndo as $namaBulan)
                <option value="{{ $namaBulan }}" {{ $selectedMonth === $namaBulan ? 'selected' : '' }}>{{ $namaBulan }}</option>
            @endforeach
        </select>

        @if ($selectedYear !== 'all' || $selectedMonth !== 'all')
            <a href="{{ url()->current() }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Reset Filter
            </a>
        @endif
    </form>

    <!-- ================= JUDUL & DESKRIPSI (baris sendiri, di bawah filter) ================= -->
    <div class="mb-6">
        <nav class="text-sm text-gray-500 mb-2 flex items-center gap-2">
            <a href="{{ url('/') }}" class="hover:text-blue-600">Dashboard</a>
            <span class="text-gray-400">/</span>
            <span class="text-blue-600 font-medium">Summary</span>
        </nav>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1.5">Realisasi Kegiatan Rutin/Strategis</h2>
        <p class="text-sm text-gray-500 max-w-3xl">Rekapitulasi Kegiatan Rutin/Strategis Departemen Administrasi Korporat, digambarkan dalam tabel sebagai berikut.</p>
    </div>

    <!-- ================= TABEL SUMMARY ================= -->
    <x-card class="!rounded-2xl overflow-visible !p-0 shadow-md border border-gray-100 bg-white">

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-pkt-jingga to-orange-500">
                        <th class="px-4 py-4 text-xs font-bold text-white text-center whitespace-nowrap sticky left-0 bg-pkt-jingga z-10 shadow-[2px_0_4px_rgba(0,0,0,0.06)]">No</th>
                        <th class="px-4 py-4 text-xs font-bold text-white text-center whitespace-nowrap">Tahun</th>
                        <th class="px-4 py-4 text-xs font-bold text-white text-center whitespace-nowrap">Bulan</th>
                        @foreach ($metrics as $def)
                            <th class="px-4 py-4 text-xs font-bold text-white text-center whitespace-nowrap min-w-[140px]">{{ $def['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($tableRows as $row)
                        <tr class="{{ $loop->even ? 'bg-gray-50/50' : '' }} hover:bg-blue-50/50 transition-colors group">
                            <td class="px-4 py-3 text-center text-gray-400 font-mono text-xs sticky left-0 bg-white group-hover:bg-blue-50/50 {{ $loop->even ? '!bg-gray-50/50 group-hover:!bg-blue-50/50' : '' }} shadow-[2px_0_4px_rgba(0,0,0,0.03)]">
                                {{ ($tableRows->currentPage() - 1) * $tableRows->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 font-semibold whitespace-nowrap">{{ $row['tahun'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-700 whitespace-nowrap">{{ $row['bulan'] }}</td>

                            @foreach ($metrics as $key => $def)
                                @php
                                    $value = $row['values'][$key];
                                    $percent = $value > 0 ? max(6, round($value / $maxPerColumn[$key] * 100)) : 0;
                                @endphp
                                <td class="px-4 py-3 align-middle">
                                    <div class="flex flex-col items-center gap-1.5 min-w-[110px]">
                                        <span class="text-xs font-semibold text-gray-700 font-mono tabular-nums">{{ $value > 0 ? number_format($value, 0, ',', '.') : '-' }}</span>
                                        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-visible">
                                            @if ($value > 0)
                                                <div class="h-full rounded-full transition-all" style="width: {{ $percent }}%; background-color: {{ $def['color'] }};"></div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($metrics) + 3 }}" class="px-6 py-14 text-center text-gray-500 text-sm">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                Belum ada data untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($tableRows->isNotEmpty())
                    <tfoot>
                        <tr class="bg-blue-50/60 border-t-2 border-blue-100 font-bold">
                            <td class="px-4 py-4 sticky left-0 bg-blue-50/60"></td>
                            <td colspan="2" class="px-4 py-4 text-right text-gray-800 whitespace-nowrap">Total Keseluruhan</td>
                            @foreach ($metrics as $key => $def)
                                <td class="px-4 py-4 text-center text-gray-900 font-mono tabular-nums">{{ number_format($totals[$key], 0, ',', '.') }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination Footer -->
        @if ($tableRows->total() > 0)
            <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-500 bg-white">
                <div>Menampilkan {{ $tableRows->firstItem() }}–{{ $tableRows->lastItem() }} dari {{ $tableRows->total() }} baris</div>
                <div class="flex items-center gap-1">
                    <a href="{{ $tableRows->previousPageUrl() ?? '#' }}"
                       class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 {{ $tableRows->onFirstPage() ? 'text-gray-300 bg-gray-50 pointer-events-none' : 'text-gray-700 hover:bg-gray-50' }}">&lt;</a>

                    @php
                        $start = max(1, $tableRows->currentPage() - 2);
                        $end = min($tableRows->lastPage(), $tableRows->currentPage() + 2);
                    @endphp

                    @for ($page = $start; $page <= $end; $page++)
                        <a href="{{ $tableRows->url($page) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-xl font-medium {{ $page === $tableRows->currentPage() ? 'bg-pkt-biru text-white' : 'border border-gray-200 hover:bg-gray-50 text-gray-700' }}">{{ $page }}</a>
                    @endfor

                    <a href="{{ $tableRows->nextPageUrl() ?? '#' }}"
                       class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 {{ $tableRows->hasMorePages() ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-300 bg-gray-50 pointer-events-none' }}">&gt;</a>
                </div>
            </div>
        @endif

    </x-card>

    <p class="text-xs text-gray-400 mt-4">
        Data bersifat read-only, diambil otomatis dari modul Perizinan, Pelaporan, BA/SK/Memo, Pengiriman Dokumen, Undangan, DOF, Arsip, dan Teknikal File.
    </p>

</main>
@endsection
