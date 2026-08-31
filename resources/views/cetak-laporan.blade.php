@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Cetak Laporan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Cetak Laporan Kinerja Bulanan</h2>
            <p class="text-sm text-gray-500 font-medium">Pilih periode laporan dan lengkapi data penandatangan untuk PDF.</p>
        </div>
    </div>

    <div class="max-w-3xl">

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm flex flex-col shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="font-bold">Gagal memproses laporan:</span>
                </div>
                <ul class="list-disc list-inside pl-7 text-xs">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <form action="{{ route('cetak.laporan.pdf') }}" method="POST" target="_blank" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Periode <span class="text-red-500">*</span></label>
                        <select name="tahun" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none cursor-pointer bg-gray-50">
                            @for($i = date('Y') + 1; $i >= 2024; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan Periode <span class="text-red-500">*</span></label>
                        <select name="bulan" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none cursor-pointer bg-gray-50">
                            @php $bulanIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('F'); @endphp
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                                <option value="{{ $b }}" {{ $bulanIni == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t border-gray-100 my-6"></div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Penandatangan (VP) <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_vp" value="Ratna Wydiyanti" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none font-bold text-gray-900 bg-white">
                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="py-3 px-8 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-colors w-full md:w-auto shadow-lg shadow-red-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Generate & Lihat PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
