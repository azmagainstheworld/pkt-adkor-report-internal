@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <!-- Header & Breadcrumb -->
    <div class="mb-6">
        <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
            <a href="#" class="hover:text-blue-600">Dashboard</a>
            <span class="text-gray-400">/</span>
            <a href="{{ route('surat.index') }}" class="hover:text-blue-600">Surat Masuk & Keluar</a>
            <span class="text-gray-400">/</span>
            <span class="text-blue-600 font-medium">Detail Berkas</span>
        </nav>
        <div class="flex justify-between items-end flex-wrap gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">Detail Arsip Surat</h2>
            </div>
            <a href="{{ route('surat.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Tabel
            </a>
        </div>
    </div>

    <!-- Tabel Lengkap Detail Surat -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-visible mb-6">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Tabel Rincian Surat Satuan</h3>
            <p class="text-xs text-gray-400">Menampilkan detail data lengkap termasuk Drafter dan kolom tambahan</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">No.</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">Tahun</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">Bulan</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">Nomor Surat</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">Tanggal Surat</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap bg-indigo-50 text-indigo-700">Drafter</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">Judul Surat</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">Status Upload</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">Status</th>
                        <th class="px-6 py-3.5 font-semibold whitespace-nowrap">Jenis Surat</th>
                        
                        <!-- Header Kolom Dinamis -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <th class="px-6 py-3.5 font-semibold whitespace-nowrap bg-gray-100">{{ $kolom->nama_kolom }}</th>
                            @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors text-xs">
                        <td class="px-6 py-4 text-gray-500 font-medium">1</td>
                        <td class="px-6 py-4">{{ $surat->tahun }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $surat->bulan }}</td>
                        <td class="px-6 py-4 font-mono text-gray-800">{{ $surat->nomor_surat }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d/m/Y') : '-' }}</td>
                        
                        <!-- Drafter (Khusus di halaman ini) -->
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-indigo-700">{{ $surat->drafter ?: '-' }}</td>
                        
                        <td class="px-6 py-4 min-w-[200px]">{{ $surat->judul_surat }}</td>
                        
                        <!-- Status Upload (File Digital) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($surat->file_path)
                                <a href="{{ Storage::url($surat->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Unduh File
                                </a>
                            @else
                                <span class="text-gray-400 italic">Kosong</span>
                            @endif
                        </td>
                        
                        <!-- Status Pengiriman -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $surat->status == 'Terkirim' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $surat->status }}
                            </span>
                        </td>
                        
                        <!-- Jenis Surat -->
                        <td class="px-6 py-4 whitespace-nowrap font-bold">
                            @if($surat->jenis_surat == 'Surat Masuk')
                                <span class="text-[#0056A3]">Masuk</span>
                            @else
                                <span class="text-[#F7941E]">Keluar</span>
                            @endif
                        </td>
                        
                        <!-- Isi Data Tambahan (Kolom Dinamis) -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-6 py-4 whitespace-nowrap align-middle">
                                    @if($kolom->tipe_input === 'currency' && isset($dataTambahan[$kolom->nama_kolom]))
                                        Rp {{ number_format($dataTambahan[$kolom->nama_kolom], 0, ',', '.') }}
                                    @else
                                        {{ $dataTambahan[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</main>
@endsection
