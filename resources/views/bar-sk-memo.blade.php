@extends('layouts.app')

@section('title', 'BAR SK Memo')

@section('content')

<style>
.hide-bulk-terbit th:first-child, .hide-bulk-terbit td:first-child { display: none !important; }
.hide-bulk-proses th:first-child, .hide-bulk-proses td:first-child { display: none !important; }
</style>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    <!-- MODAL ERROR KUSTOM -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Peringatan</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors">Mengerti</button>
        </div>
    </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm flex flex-col shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-bold">Gagal menyimpan data:</span>
            </div>
            <ul class="list-disc list-inside pl-8 text-xs">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">BAR SK Memo</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">BAR SK Memo</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('bar-sk-memo.index') }}" method="GET" id="filterForm" class="flex items-center gap-3">
            <select name="tahun" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm outline-none focus:border-blue-500 cursor-pointer">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>

            @php
                $bulanDariDB = \Illuminate\Support\Facades\DB::table('bar_sk_memo')->select('bulan')->distinct()->pluck('bulan')->toArray();
                $semuaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $bulanTampil = count($bulanDariDB) > 0 ? $bulanDariDB : $semuaBulan;
                usort($bulanTampil, function($a, $b) use ($semuaBulan) { return array_search($a, $semuaBulan) <=> array_search($b, $semuaBulan); });
            @endphp

            <select name="bulan" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm outline-none focus:border-blue-500 cursor-pointer">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach($bulanTampil as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHARTS SECTION (GRID SIDE-BY-SIDE) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        <!-- CHART 1: BAR SK MEMO PROSES -->
        <div class="flex flex-col">
            <div class="h-96 relative z-10 flex-1">
                <x-dynamic-chart title="BAR SK MEMO PROSES" subtitle="Distribusi dokumen proses per bulan (Semua Tahun)" type="bar" id="chartProses" />
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 mt-3 bg-white py-3 px-4 rounded-xl shadow-sm border border-gray-100 text-xs">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#BAE6FD]"></span><span class="text-gray-700 font-medium">Proses BAR Monitoring</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#3B82F6]"></span><span class="text-gray-700 font-medium">Proses Memo Direksi</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#22C55E]"></span><span class="text-gray-700 font-medium">Proses SKD Kep. Bersama</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#F97316]"></span><span class="text-gray-700 font-medium">Proses SKD Non Ratifikasi</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#1E3A8A]"></span><span class="text-gray-700 font-medium">Proses SKD Ratifikasi</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#FDE047]"></span><span class="text-gray-700 font-medium">Proses BAR Manajemen</span></div>
            </div>
        </div>

        <!-- CHART 2: BAR SK MEMO TERBIT -->
        <div class="flex flex-col">
            <div class="h-96 relative z-10 flex-1">
                <x-dynamic-chart title="BAR SK MEMO TERBIT" subtitle="Distribusi dokumen terbit per bulan (Semua Tahun)" type="bar" id="chartTerbit" />
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 mt-3 bg-white py-3 px-4 rounded-xl shadow-sm border border-gray-100 text-xs">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#BAE6FD]"></span><span class="text-gray-700 font-medium">BAR Monitoring</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#3B82F6]"></span><span class="text-gray-700 font-medium">Memo Direksi</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#22C55E]"></span><span class="text-gray-700 font-medium">SKD Kep. Bersama</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#F97316]"></span><span class="text-gray-700 font-medium">SKD Non Ratifikasi</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#1E3A8A]"></span><span class="text-gray-700 font-medium">SKD Ratifikasi</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#FDE047]"></span><span class="text-gray-700 font-medium">BAR Manajemen</span></div>
            </div>
        </div>
    </div>

    <div class="my-10 relative">
        <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300"></div></div>
        <div class="relative flex justify-center"><span class="bg-[#F8F9FA] px-4 text-sm font-bold text-gray-500 tracking-wide uppercase">Rincian Data Dokumen</span></div>
    </div>

    <!-- ================= TABLES SECTION (ATAS BAWAH - FULL WIDTH) ================= -->
    <div class="space-y-10 mb-10">
        @include('bar-sk-memo.partials.table-proses')
        @include('bar-sk-memo.partials.table-terbit')
    </div>

    <!-- MODALS SECTION -->
    @include('bar-sk-memo.partials.modals')

</main>

@include('bar-sk-memo.partials.scripts')

@endsection
