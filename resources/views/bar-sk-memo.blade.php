@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

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
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Berita Acara, SK & Memo</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('bar-sk-memo.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm outline-none focus:border-orange-500 cursor-pointer">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm outline-none focus:border-orange-500 cursor-pointer">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART ================= -->
    <x-card class="!rounded-xl overflow-hidden shadow-sm border border-gray-100 bg-white mb-8 p-6">
        <div class="mb-4 flex flex-wrap gap-3 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
            <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-sm bg-[#1E3A8A]"></span><span>BA Proses</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-sm bg-[#60A5FA]"></span><span>BA Terbit</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-sm bg-[#BFDBFE]"></span><span>SK Direksi Proses</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-sm bg-[#DC2626]"></span><span>SK Direksi Terbit</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-sm bg-[#F97316]"></span><span>Memo Direksi Proses</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-sm bg-[#FDE047]"></span><span>Memo Direksi Terbit</span></div>
        </div>
        <div class="h-80 w-full relative">
            <canvas id="barSkChart"></canvas>
        </div>
    </x-card>

    <!-- ================= TABEL 1: RINGKASAN AKUMULASI ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="overflow-x-auto">
            <x-table :headers="['Tahun', 'Bulan', 'BA Terbit', 'BA Proses', 'SK Direksi Terbit', 'SK Direksi Proses', 'Memo Direksi Terbit', 'Memo Direksi Proses']">
                @forelse($dataRekapTable as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $row->tahun }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $row->bulan }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $row->ba_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $row->ba_proses }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $row->sk_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $row->sk_proses }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $row->memo_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $row->memo_proses }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data rekapitulasi.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL 2: DOKUMEN TERBIT ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-between items-center">
            <h3 class="font-bold text-orange-900 text-sm">Rincian Dokumen Terbit</h3>
            <x-button variant="primary" onclick="openModalTambah('Terbit')" class="!py-1.5 !px-3 text-xs">Tambah Terbit</x-button>
        </div>
        <div class="overflow-x-auto">
            @php
                $headTerbit = ['Tahun', 'Bulan'];
                foreach($masterTerbit as $master) { $headTerbit[] = $master->nama_dokumen; }
                $headTerbit[] = 'Aksi';
            @endphp
            <x-table :headers="$headTerbit">
                @forelse($dataTerbitTable as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $row['bulan'] }}</td>
                        @foreach($masterTerbit as $master)
                            <td class="px-4 py-3 text-gray-600">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach
                        <td class="px-4 py-3">
                            <div class="flex gap-2 justify-center">
                                @php $itemsJson = json_encode($row['items']); @endphp
                                <button type="button" onclick="editBulan('Terbit', '{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('bar-sk-memo.destroyBulan', ['tipe' => 'Terbit', 'tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headTerbit) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL 3: DOKUMEN PROSES ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-900 text-sm">Rincian Dokumen Proses</h3>
            <x-button variant="primary" onclick="openModalTambah('Proses')" class="!py-1.5 !px-3 text-xs">Tambah Proses</x-button>
        </div>
        <div class="overflow-x-auto">
            @php
                $headProses = ['Tahun', 'Bulan'];
                foreach($masterProses as $master) { $headProses[] = $master->nama_dokumen; }
                $headProses[] = 'Aksi';
            @endphp
            <x-table :headers="$headProses">
                @forelse($dataProsesTable as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $row['bulan'] }}</td>
                        @foreach($masterProses as $master)
                            <td class="px-4 py-3 text-gray-600">{{ $row['items'][$master->id] ?? 0 }}</td>
                        @endforeach
                        <td class="px-4 py-3">
                            <div class="flex gap-2 justify-center">
                                @php $itemsJson = json_encode($row['items']); @endphp
                                <button type="button" onclick="editBulan('Proses', '{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('bar-sk-memo.destroyBulan', ['tipe' => 'Proses', 'tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headProses) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- GARIS PEMBATAS UNTUK BAGIAN SKD DAN MEMO BARU -->
    <div class="my-10 relative">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-[#F8F9FA] px-4 text-lg font-bold text-gray-900 tracking-wide uppercase">DATA SURAT KEPUTUSAN DIREKSI dan MEMO DIREKSI TAHUN {{ $filterTahun == 'semua' ? date('Y') : $filterTahun }}</span>
        </div>
    </div>

    <!-- ================= TABEL BARU 1: SKD TERBIT ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Surat Keputusan Direksi Terbit</h3>
            </div>
            <x-button variant="primary" onclick="openModalSkdTerbit()" class="!py-2 text-xs">Tambah SKD Terbit</x-button>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['No.', 'Nomor SKD', 'Tentang', 'Tanggal Penetapan', 'Tanggal Salinan', 'Drafter', 'Kategori', 'Aksi']">
                @forelse($dataSkdTerbit as $index => $skd)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $skd->nomor_skd }}</td>
                        <td class="px-4 py-3 text-gray-600 truncate max-w-xs text-center" title="{{ $skd->tentang }}">{{ $skd->tentang }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ Carbon\Carbon::parse($skd->tanggal_penetapan)->locale('id')->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $skd->tanggal_salinan ? Carbon\Carbon::parse($skd->tanggal_salinan)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $skd->drafter }}</td>
                        <td class="px-4 py-3 text-gray-600 font-bold uppercase text-center">{{ $skd->kategori }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editSkdTerbit({{ $skd }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('skd-terbit.destroy', $skd->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500 text-sm">Data SKD Terbit kosong.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL BARU 2: SKD PROSES ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Surat Keputusan Direksi Proses</h3>
            </div>
            <x-button variant="primary" onclick="openModalSkdProses()" class="!py-2 text-xs">Tambah SKD Proses</x-button>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['No.', 'Tanggal Permintaan', 'Tentang', 'Unit Kerja Peminta', 'Kategori', 'Aksi']">
                @forelse($dataSkdProses as $index => $skd)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ Carbon\Carbon::parse($skd->tanggal_permintaan)->locale('id')->translatedFormat('j-M-Y') }}</td>
                        <td class="px-4 py-3 text-gray-600 truncate max-w-xs text-center" title="{{ $skd->tentang }}">{{ $skd->tentang }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $skd->unit_kerja_peminta }}</td>
                        <td class="px-4 py-3 text-gray-600 font-bold uppercase text-center">{{ $skd->kategori }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editSkdProses({{ $skd }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('skd-proses.destroy', $skd->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">Data SKD Proses kosong.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL BARU 3: MEMO DIREKSI TERBIT ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Memo Direksi Terbit</h3>
            </div>
            <x-button variant="primary" onclick="openModalMemoTerbit()" class="!py-2 text-xs">Tambah Memo Terbit</x-button>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['No.', 'Nomor Memo', 'Tentang', 'Tanggal', 'Aksi']">
                @forelse($dataMemoTerbit as $index => $memo)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $memo->nomor_memo }}</td>
                        <td class="px-4 py-3 text-gray-600 truncate max-w-xs text-center" title="{{ $memo->tentang }}">{{ $memo->tentang }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ Carbon\Carbon::parse($memo->tanggal)->locale('id')->translatedFormat('d-M-y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editMemoTerbit({{ $memo }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('memo-terbit.destroy', $memo->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">Data Memo Terbit kosong.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL BARU 4: MEMO DIREKSI PROSES ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Memo Direksi Proses</h3>
            </div>
            <x-button variant="primary" onclick="openModalMemoProses()" class="!py-2 text-xs">Tambah Memo Proses</x-button>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['No.', 'Tanggal Permintaan', 'Tentang', 'Unit Kerja Peminta', 'Aksi']">
                @forelse($dataMemoProses as $index => $memo)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ Carbon\Carbon::parse($memo->tanggal_permintaan)->locale('id')->translatedFormat('d-M-Y') }}</td>
                        <td class="px-4 py-3 text-gray-600 truncate max-w-xs text-center" title="{{ $memo->tentang }}">{{ $memo->tentang }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $memo->unit_kerja_peminta }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editMemoProses({{ $memo }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('memo-proses.destroy', $memo->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">Data Memo Proses kosong.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data" message="Data yang dihapus tidak dapat dikembalikan. Lanjutkan?" />

    <!-- MODAL TAMBAH (TERBIT & PROSES) DINAMIS -->
    <x-modal id="modalTambah" title="Tambah Data Dokumen" description="Pilih jenis dokumen dan masukkan jumlahnya.">
        <form action="{{ route('bar-sk-memo.storeDokumen') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="tipe" id="add_tipe">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_tambah" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'add_tahun', 'add_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="add_tahun">
                <input type="hidden" name="bulan" id="add_bulan">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Dokumen <span class="text-red-500">*</span></label>
                <select name="dokumen_id" id="add_dokumen_id" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="toggleDokumenBaru(this.value)">
                    <option value="" disabled selected>Pilih Jenis...</option>
                    <option value="tambah_baru" class="font-bold text-orange-600">++ Tambah Variabel Baru ++</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bidang ini wajib dipilih!</span>
            </div>
            <div class="md:col-span-2 hidden" id="wrap_dokumen_baru">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Variabel Baru <span class="text-red-500">*</span></label>
                <input type="text" name="nama_dokumen_baru" id="input_dokumen_baru" placeholder="Ketik nama dokumen..." class="w-full px-4 py-2 bg-orange-50 border border-orange-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" required placeholder="Contoh: 5" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Jumlah wajib diisi!</span>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambah')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL EDIT BULAN (TERBIT & PROSES) -->
    <x-modal id="modalEditBulan" title="Edit Data Bulan" description="Perbarui seluruh data pada bulan terkait.">
        <form action="{{ route('bar-sk-memo.updateBulan') }}" method="POST" id="formEditBulan" class="grid grid-cols-2 gap-x-4 gap-y-4 novalidate-form" novalidate>
            @csrf
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_edit" required class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500" onchange="syncPeriode(this.value, 'edit_tahun', 'edit_bulan')">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                <input type="hidden" name="tahun" id="edit_tahun">
                <input type="hidden" name="bulan" id="edit_bulan">
            </div>
            <div class="col-span-2 border-b border-gray-100 my-1"></div>
            <div id="edit_dynamic_inputs" class="col-span-2 grid grid-cols-2 gap-4"></div>
            <div class="col-span-2 flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEditBulan')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL SKD TERBIT -->
    <x-modal id="modalSkdTerbit" title="Formulir SKD Terbit" description="Masukkan detail surat keputusan direksi.">
        <form action="{{ route('skd-terbit.store') }}" method="POST" id="formSkdTerbit" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodSkdTerbit" value="POST">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor SKD <span class="text-red-500">*</span></label>
                <input type="text" name="nomor_skd" id="skdt_nomor" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori" id="skdt_kategori" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                    <option value="" disabled selected>Pilih Kategori...</option>
                    <option value="Ratifikasi">Ratifikasi</option>
                    <option value="Non Ratifikasi">Non Ratifikasi</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tentang <span class="text-red-500">*</span></label>
                <textarea name="tentang" id="skdt_tentang" rows="2" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Penetapan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_penetapan" id="skdt_penetapan" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Salinan</label>
                <input type="date" name="tanggal_salinan" id="skdt_salinan" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Drafter <span class="text-red-500">*</span></label>
                <input type="text" name="drafter" id="skdt_drafter" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalSkdTerbit')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL SKD PROSES -->
    <x-modal id="modalSkdProses" title="Formulir SKD Proses" description="Masukkan detail proses SKD.">
        <form action="{{ route('skd-proses.store') }}" method="POST" id="formSkdProses" class="grid grid-cols-1 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodSkdProses" value="POST">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Permintaan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_permintaan" id="skdp_tanggal" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori" id="skdp_kategori" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                    <option value="" disabled selected>Pilih Kategori...</option>
                    <option value="Ratifikasi">Ratifikasi</option>
                    <option value="Non Ratifikasi">Non Ratifikasi</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tentang <span class="text-red-500">*</span></label>
                <textarea name="tentang" id="skdp_tentang" rows="2" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Unit Kerja Peminta <span class="text-red-500">*</span></label>
                <input type="text" name="unit_kerja_peminta" id="skdp_unit" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div class="flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalSkdProses')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL MEMO TERBIT -->
    <x-modal id="modalMemoTerbit" title="Formulir Memo Terbit" description="Masukkan detail Memo Direksi.">
        <form action="{{ route('memo-terbit.store') }}" method="POST" id="formMemoTerbit" class="grid grid-cols-1 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodMemoTerbit" value="POST">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Memo <span class="text-red-500">*</span></label>
                <input type="text" name="nomor_memo" id="memt_nomor" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="memt_tanggal" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tentang <span class="text-red-500">*</span></label>
                <textarea name="tentang" id="memt_tentang" rows="2" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div class="flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalMemoTerbit')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL MEMO PROSES -->
    <x-modal id="modalMemoProses" title="Formulir Memo Proses" description="Masukkan detail Memo Direksi Proses.">
        <form action="{{ route('memo-proses.store') }}" method="POST" id="formMemoProses" class="grid grid-cols-1 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodMemoProses" value="POST">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Permintaan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_permintaan" id="memp_tanggal" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tentang <span class="text-red-500">*</span></label>
                <textarea name="tentang" id="memp_tentang" rows="2" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Unit Kerja Peminta <span class="text-red-500">*</span></label>
                <input type="text" name="unit_kerja_peminta" id="memp_unit" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div class="flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalMemoProses')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>
</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // ================= Parsing Kalendar Bulan =================
    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
            if(event && event.target) {
                const picker = event.target;
                picker.classList.remove('border-red-500', 'bg-red-50');
                const err = picker.nextElementSibling;
                if(err && err.classList.contains('error-msg')) err.classList.add('hidden');
            }
        }
    }
    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) {
            return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`;
        }
        return '';
    }

    const masterTerbit = {!! json_encode($masterTerbit) !!};
    const masterProses = {!! json_encode($masterProses) !!};

    function toggleDokumenBaru(value) {
        const wrap = document.getElementById('wrap_dokumen_baru');
        const input = document.getElementById('input_dokumen_baru');
        if (value === 'tambah_baru') { 
            wrap.classList.remove('hidden'); input.setAttribute('required', 'required'); 
        } else { 
            wrap.classList.add('hidden'); input.removeAttribute('required'); 
            input.classList.remove('border-red-500', 'bg-red-50');
            if (input.nextElementSibling) input.nextElementSibling.classList.add('hidden');
        }
    }

    function openModalTambah(tipe) {
        document.getElementById('add_tipe').value = tipe;
        const select = document.getElementById('add_dokumen_id');
        select.innerHTML = '<option value="" disabled selected>Pilih Jenis...</option>';
        const masters = tipe === 'Terbit' ? masterTerbit : masterProses;
        masters.forEach(m => { select.innerHTML += `<option value="${m.id}">${m.nama_dokumen}</option>`; });
        select.innerHTML += '<option value="tambah_baru" class="font-bold text-orange-600">++ Tambah Variabel Baru ++</option>';
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah').value = currentMonth;
        syncPeriode(currentMonth, 'add_tahun', 'add_bulan');
        openModal('modalTambah');
    }

    function editBulan(tipe, tahun, bulan, itemsJson) {
        const pickerVal = getMonthPickerValue(tahun, bulan);
        document.getElementById('picker_edit').value = pickerVal;
        syncPeriode(pickerVal, 'edit_tahun', 'edit_bulan');
        const items = JSON.parse(itemsJson);
        const container = document.getElementById('edit_dynamic_inputs');
        container.innerHTML = '';
        const masters = tipe === 'Terbit' ? masterTerbit : masterProses;
        masters.forEach(m => {
            const val = items[m.id] || 0;
            container.innerHTML += `
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 truncate" title="${m.nama_dokumen}">${m.nama_dokumen}</label>
                    <input type="number" name="items[${m.id}]" value="${val}" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                </div>
            `;
        });
        openModal('modalEditBulan');
    }

    // ================= SKD TERBIT =================
    const formSkdTerbit = document.getElementById('formSkdTerbit');
    const methodSkdTerbit = document.getElementById('methodSkdTerbit');
    function openModalSkdTerbit() {
        formSkdTerbit.action = "{{ route('skd-terbit.store') }}";
        methodSkdTerbit.value = "POST";
        formSkdTerbit.reset(); openModal('modalSkdTerbit');
    }
    function editSkdTerbit(data) {
        formSkdTerbit.action = "/administrasi/bar-sk-memo/skd-terbit/" + data.id;
        methodSkdTerbit.value = "PUT";
        document.getElementById('skdt_nomor').value = data.nomor_skd;
        document.getElementById('skdt_kategori').value = data.kategori;
        document.getElementById('skdt_tentang').value = data.tentang;
        document.getElementById('skdt_penetapan').value = data.tanggal_penetapan;
        document.getElementById('skdt_salinan').value = data.tanggal_salinan || '';
        document.getElementById('skdt_drafter').value = data.drafter;
        openModal('modalSkdTerbit');
    }

    // ================= SKD PROSES =================
    const formSkdProses = document.getElementById('formSkdProses');
    const methodSkdProses = document.getElementById('methodSkdProses');
    function openModalSkdProses() {
        formSkdProses.action = "{{ route('skd-proses.store') }}";
        methodSkdProses.value = "POST";
        formSkdProses.reset(); openModal('modalSkdProses');
    }
    function editSkdProses(data) {
        formSkdProses.action = "/administrasi/bar-sk-memo/skd-proses/" + data.id;
        methodSkdProses.value = "PUT";
        document.getElementById('skdp_tanggal').value = data.tanggal_permintaan;
        document.getElementById('skdp_kategori').value = data.kategori;
        document.getElementById('skdp_tentang').value = data.tentang;
        document.getElementById('skdp_unit').value = data.unit_kerja_peminta;
        openModal('modalSkdProses');
    }

    // ================= MEMO TERBIT =================
    const formMemoTerbit = document.getElementById('formMemoTerbit');
    const methodMemoTerbit = document.getElementById('methodMemoTerbit');
    function openModalMemoTerbit() {
        formMemoTerbit.action = "{{ route('memo-terbit.store') }}";
        methodMemoTerbit.value = "POST";
        formMemoTerbit.reset(); openModal('modalMemoTerbit');
    }
    function editMemoTerbit(data) {
        formMemoTerbit.action = "/administrasi/bar-sk-memo/memo-terbit/" + data.id;
        methodMemoTerbit.value = "PUT";
        document.getElementById('memt_nomor').value = data.nomor_memo;
        document.getElementById('memt_tanggal').value = data.tanggal;
        document.getElementById('memt_tentang').value = data.tentang;
        openModal('modalMemoTerbit');
    }

    // ================= MEMO PROSES =================
    const formMemoProses = document.getElementById('formMemoProses');
    const methodMemoProses = document.getElementById('methodMemoProses');
    function openModalMemoProses() {
        formMemoProses.action = "{{ route('memo-proses.store') }}";
        methodMemoProses.value = "POST";
        formMemoProses.reset(); openModal('modalMemoProses');
    }
    function editMemoProses(data) {
        formMemoProses.action = "/administrasi/bar-sk-memo/memo-proses/" + data.id;
        methodMemoProses.value = "PUT";
        document.getElementById('memp_tanggal').value = data.tanggal_permintaan;
        document.getElementById('memp_tentang').value = data.tentang;
        document.getElementById('memp_unit').value = data.unit_kerja_peminta;
        openModal('modalMemoProses');
    }

    // ================= Validasi Teks Merah =================
    const forms = document.querySelectorAll('.novalidate-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500', 'bg-red-50'); 
                    field.classList.remove('border-gray-300', 'border-orange-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden'); 
                } else {
                    field.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden'); 
                }
            });
            if (!isValid) e.preventDefault(); 
        });
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
    });

    // ================= Chart.js Stacked Bar =================
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('barSkChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const rawChartData = {!! json_encode($chartData) !!};
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: rawChartData.map(d => d.label),
                    datasets: [
                        { label: 'BA Proses', data: rawChartData.map(d => d.ba_proses), backgroundColor: '#1E3A8A', stack: 'Stack 0' },
                        { label: 'BA Terbit', data: rawChartData.map(d => d.ba_terbit), backgroundColor: '#60A5FA', stack: 'Stack 0' },
                        { label: 'SK Direksi Proses', data: rawChartData.map(d => d.sk_proses), backgroundColor: '#BFDBFE', stack: 'Stack 0' },
                        { label: 'SK Direksi Terbit', data: rawChartData.map(d => d.sk_terbit), backgroundColor: '#DC2626', stack: 'Stack 0' },
                        { label: 'Memo Direksi Proses', data: rawChartData.map(d => d.memo_proses), backgroundColor: '#F97316', stack: 'Stack 0' },
                        { label: 'Memo Direksi Terbit', data: rawChartData.map(d => d.memo_terbit), backgroundColor: '#FDE047', stack: 'Stack 0' },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { stacked: true, beginAtZero: true, grid: { color: '#F3F4F6', drawBorder: false } },
                        x: { stacked: true, grid: { display: false, drawBorder: false } }
                    }
                }
            });
        }
    });
</script>
@endsection