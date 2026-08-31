@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <!-- KOMPONEN MODAL SUKSES ANDALAN ZAHRA ✨ -->
    <x-success-modal />

    <!-- MODAL ERROR KUSTOM -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Gagal Memproses</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Tutup
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex flex-col shadow-sm">
            <span class="font-bold mb-2">Gagal memproses data. Periksa inputan Anda:</span>
            <ul class="list-disc list-inside pl-4 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Section & Filter (Req 1) -->
    <div class="flex justify-between items-end mb-6 gap-4 flex-wrap">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Surat Masuk & Keluar</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Surat Masuk & Surat Keluar</h2>
            <p class="text-sm text-gray-500 font-medium">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
        
        <!-- Filter Dinamis -->
        <form action="{{ route('surat.index') }}" method="GET" class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-gray-100">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 cursor-pointer outline-none focus:border-blue-500">
                <option value="semua" {{ $tahunFilter == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $tahunFilter == $thn ? 'selected' : '' }}>Tahun {{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 cursor-pointer outline-none focus:border-blue-500">
                <option value="semua" {{ $bulanFilter == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach($bulanTersedia as $b)
                    <option value="{{ $b }}" {{ $bulanFilter == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Req 2 & 3: CHART SECTION MENGGUNAKAN KOMPONEN DYNAMIC CHART -->
    <div class="mb-8">
        <x-dynamic-chart 
            id="chartSurat" 
            title="Tren Distribusi Surat {{ $tahunFilter == 'semua' ? 'Keseluruhan' : 'Tahun '.$tahunFilter }}" 
            subtitle="Menampilkan 12 bulan penuh (Status Terkirim) untuk perbandingan tren" 
            type="bar">
        </x-dynamic-chart>
    </div>

    <!-- TABEL 1 (REKAPITULASI - READ ONLY) -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Akumulasi Laporan Surat Masuk dan Kekuar</h3>
            <p class="text-xs text-gray-400">Total surat berstatus <span class="text-green-600 font-medium">"Terkirim"</span> terfilter Tahun: {{ $tahunFilter }}, Bulan: {{ $bulanFilter }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold">Tahun</th>
                        <th class="px-6 py-3.5 font-semibold">Bulan</th>
                        <th class="px-6 py-3.5 font-semibold bg-blue-50/50 text-[#0056A3]">Total Surat Masuk</th>
                        <th class="px-6 py-3.5 font-semibold bg-orange-50/50 text-[#F7941E]">Total Surat Keluar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekapData as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ $row->tahun }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $row->bulan }}</td>
                            <td class="px-6 py-4 text-[#0056A3] font-bold">{{ $row->total_masuk }} Berkas</td>
                            <td class="px-6 py-4 text-[#F7941E] font-bold">{{ $row->total_keluar }} Berkas</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Tidak ada data rekap untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if($rekapData->isNotEmpty())
                    <tfoot class="bg-gray-100 font-bold text-gray-900 border-t border-gray-200">
                        <tr>
                            <td class="px-6 py-4 text-right" colspan="2">GRAND TOTAL (Periode Terpilih) :</td>
                            <td class="px-6 py-4 text-[#0056A3]">{{ $grandTotalMasuk }} Berkas</td>
                            <td class="px-6 py-4 text-[#F7941E]">{{ $grandTotalKeluar }} Berkas</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-card>

    <!-- ================= Req 4: ACTION BAR DIPINDAH KE ATAS TABEL 2 ================= -->
    <div class="flex justify-end items-center mb-5">
        <div class="flex flex-row items-center gap-3">
            
            <!-- Opsi Lanjutan -->
            <div class="relative inline-block text-left">
                <button type="button" onclick="toggleDropdown('dropdownOpsiSurat')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Opsi Lanjutan
                    <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Isi Dropdown (Req 7 & 8: Opsi Ekspor 3 Jenis) -->
                <div id="dropdownOpsiSurat" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                    
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor ke Excel</p>
                    </div>
                    <div class="py-1" role="none">
                        <a href="{{ route('surat.export.excel', ['jenis' => 'tabel1', 'tahun' => $tahunFilter, 'bulan' => $bulanFilter]) }}" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor Tabel 1 Saja (Rekap)
                        </a>
                        <a href="{{ route('surat.export.excel', ['jenis' => 'tabel2', 'tahun' => $tahunFilter, 'bulan' => $bulanFilter]) }}" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor Tabel 2 Saja (Detail)
                        </a>
                        <a href="{{ route('surat.export.excel', ['jenis' => 'keduanya', 'tahun' => $tahunFilter, 'bulan' => $bulanFilter]) }}" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor Tabel 1 & 2 (Semua)
                        </a>
                    </div>

                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor ke PDF</p>
                    </div>
                    <div class="py-1" role="none">
                        <a href="{{ route('surat.export.pdf', ['jenis' => 'tabel1', 'tahun' => $tahunFilter, 'bulan' => $bulanFilter]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor Tabel 1 Saja (Rekap)
                        </a>
                        <a href="{{ route('surat.export.pdf', ['jenis' => 'tabel2', 'tahun' => $tahunFilter, 'bulan' => $bulanFilter]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor Tabel 2 Saja (Detail)
                        </a>
                        <a href="{{ route('surat.export.pdf', ['jenis' => 'keduanya', 'tahun' => $tahunFilter, 'bulan' => $bulanFilter]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor Tabel 1 & 2 (Semua)
                        </a>
                    </div>
                    
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Impor & Konfigurasi</p>
                    </div>
                    <div class="py-1" role="none">
                        <button type="button" onclick="openModal('modalImportExcel'); toggleDropdown('dropdownOpsiSurat')" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Impor Data dari Excel
                        </button>
                        <button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsiSurat')" class="text-gray-700 w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom Tambahan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tambah Data Utama -->
            <div>
                <button type="button" onclick="openModalTambah()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga hover:bg-orange-600 rounded-xl shadow-sm transition-colors border-none outline-none focus:ring-2 focus:ring-orange-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Catat Surat Satuan
                </button>
            </div>
            
        </div>
    </div>

    <!-- TABEL 2 (DETAIL DATA SATUAN) -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Tabel 2: Arsip Detail Surat Satuan</h3>
            <p class="text-xs text-gray-400">Pencatatan satuan setiap berkas surat masuk dan keluar</p>
        </div>

        @php
            // Definisi Header (Req 5: Kolom File Dihapus)
            $headers = ['No', 'Tahun', 'Bulan', 'Nomor Surat', 'Tanggal Surat', 'Judul Surat', 'Status', 'Jenis Surat'];
            if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $headers[] = $k->nama_kolom; } }
            $headers[] = 'Aksi';
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-6 py-3.5 font-semibold whitespace-nowrap">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tableDetail as $index => $row)
                        @php $tambahan = $row->data_tambahan ?? []; @endphp
                        <tr class="hover:bg-gray-50 transition-colors text-xs">
                            <td class="px-6 py-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $row->tahun }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $row->bulan }}</td>
                            <td class="px-6 py-4 font-mono text-gray-800">{{ $row->nomor_surat }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $row->tanggal_surat ? $row->tanggal_surat->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4 max-w-xs truncate">{{ $row->judul_surat }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $row->status == 'Terkirim' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                @if($row->jenis_surat == 'Surat Masuk')
                                    <span class="text-[#0056A3]">Masuk</span>
                                @else
                                    <span class="text-[#F7941E]">Keluar</span>
                                @endif
                            </td>
                            
                            @if(isset($kolomDinamis))
                                @foreach($kolomDinamis as $kolom)
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                            Rp {{ number_format($tambahan[$kolom->nama_kolom], 0, ',', '.') }}
                                        @else
                                            {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                        @endif
                                    </td>
                                @endforeach
                            @endif

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex gap-1.5 justify-center">
                                    <!-- Req 6: Tombol Detail Menggunakan <a> ke Halaman Baru -->
                                    <a href="{{ route('surat.show', $row->id) }}" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg border border-blue-200" title="Lihat Halaman Detail Lengkap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <!-- Edit -->
                                    <button type="button" onclick="openEditDataSatuanModal({{ json_encode($row) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg border border-amber-200" title="Edit Data">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <!-- Hapus -->
                                    <button type="button" onclick="openDeleteModal('modalHapusSurat', '{{ route('surat.destroy', $row->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg border border-red-200" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-gray-500 py-6">Tidak ada data arsip surat satuan untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapusSurat" title="Hapus Arsip Surat" message="Apakah Anda yakin ingin menghapus arsip surat satuan ini? Data rekap bulanan akan disesuaikan otomatis." />
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel dan formulir. Lanjutkan?" />

    <!-- MODAL ATUR KOLOM -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Surat" description="Kelola kolom ekstra khusus untuk pencatatan detail surat satuan.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 max-h-48 overflow-y-auto">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar Saat Ini:</h4>
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span></p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1 hover:bg-red-50 rounded transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic text-center py-4">Belum ada kolom tambahan yang dibuat.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-5 space-y-4 novalidate-form" novalidate>
            @csrf
        <!-- Download Template injected -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="{{ route('template.download', 'surat') }}" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>
 <input type="hidden" name="modul" value="surat">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Buat Kolom Baru:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">Nama Kolom <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kolom" required placeholder="Cth: Perihal Ringkas" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Tipe Input <span class="text-red-500">*</span></label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500 cursor-pointer" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option><option value="number">Angka Biasa</option><option value="date">Tanggal</option><option value="dropdown">Pilihan (Dropdown)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Internal, Eksternal" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button><x-button variant="primary" type="submit" class="!bg-blue-600 hover:!bg-blue-700 border-none">Simpan Kolom</x-button></div>
        </form>
    </x-modal>

    <!-- MODAL TAMBAH DATA SATUAN -->
    <x-modal id="modalTambah" title="Catat Surat Satuan Baru" description="Lengkapi detail surat. Tahun dan Bulan laporan otomatis diset berdasarkan Periode Laporan.">
        <form action="{{ route('surat.store') }}" method="POST" id="formTambah" class="space-y-4 novalidate-form" novalidate>
            @csrf
        <!-- Download Template injected -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="{{ route('template.download', 'surat') }}" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Masuk Rekap Bulan/Tahun) <span class="text-red-500">*</span></label>
                <input type="month" name="periode_laporan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
            </div>
            <hr class="border-gray-100">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_surat" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_surat" required placeholder="Cth: 001/UM/XI/2023" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 font-mono">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi & unik</span>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul / Perihal Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_surat" required placeholder="Cth: Undangan Rapat Koordinasi Wilayah" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Drafter / Konseptor</label>
                    <input type="text" name="drafter" placeholder="Nama drafter" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                </div>
                <!-- Req 5: Upload Berkas Dihapus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer outline-none">
                        <option value="Terkirim" selected>Terkirim (Masuk Hitungan Rekap)</option>
                        <option value="Dibatalkan">Dibatalkan (Abaikan dari Rekap)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Surat <span class="text-red-500">*</span></label>
                    <select name="jenis_surat" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer outline-none">
                        <option value="" disabled selected>Pilih...</option>
                        <option value="Surat Masuk">Surat Masuk</option>
                        <option value="Surat Keluar">Surat Keluar</option>
                    </select>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib dipilih</span>
                </div>
            </div>

            <!-- INJEKSI KOLOM DINAMIS (TAMBAH) -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                <div class="space-y-4 pt-2 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-800">Informasi Tambahan</h4>
                    @foreach($kolomDinamis as $kolom)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'text')
                                <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            @elseif($kolom->tipe_input === 'number')
                                <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            @elseif($kolom->tipe_input === 'date')
                                <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 cursor-pointer">
                            @elseif($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 cursor-pointer outline-none">
                                    <option value="">Pilih...</option>
                                    @if($kolom->pilihan_dropdown) 
                                        @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)
                                            <option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>
                                        @endforeach 
                                    @endif
                                </select>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </form>

        <x-slot name="footer">
            <x-button variant="outline" onclick="closeModal('modalTambah')" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
            <x-button variant="primary" type="submit" form="formTambah" class="!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600 border-none">Simpan Arsip</x-button>
        </x-slot>
    </x-modal>

    <!-- MODAL EDIT DATA SATUAN -->
    <x-modal id="modalEditDataSatuan" title="Edit Arsip Surat" description="Perbarui detail surat. Perubahan periode laporan akan menyesuaikan rekap otomatis.">
        <form action="" method="POST" id="formEditDataSatuan" class="space-y-4 novalidate-form" novalidate>
            @csrf
        <!-- Download Template injected -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="{{ route('template.download', 'surat') }}" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>

            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Masuk Rekap Bulan/Tahun) <span class="text-red-500">*</span></label>
                <input type="month" name="periode_laporan" id="edit_periode_laporan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
            </div>
            <hr class="border-gray-100">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_surat" id="edit_tanggal" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_surat" id="edit_nomor" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 font-mono">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi & unik</span>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul / Perihal Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_surat" id="edit_judul" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Drafter / Konseptor</label>
                    <input type="text" name="drafter" id="edit_drafter" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                </div>
                <!-- Req 5: Upload Berkas Dihapus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer outline-none">
                        <option value="Terkirim">Terkirim</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Surat <span class="text-red-500">*</span></label>
                    <select name="jenis_surat" id="edit_jenis" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        <option value="Surat Masuk">Surat Masuk</option>
                        <option value="Surat Keluar">Surat Keluar</option>
                    </select>
                </div>
            </div>

            <!-- INJEKSI KOLOM DINAMIS (EDIT) -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                <div class="space-y-4 pt-2 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-800">Informasi Tambahan</h4>
                    @foreach($kolomDinamis as $kolom)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'text')
                                <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            @elseif($kolom->tipe_input === 'number')
                                <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            @elseif($kolom->tipe_input === 'date')
                                <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 cursor-pointer">
                            @elseif($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 outline-none">
                                    <option value="">Pilih...</option>
                                    @if($kolom->pilihan_dropdown) 
                                        @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)
                                            <option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>
                                        @endforeach 
                                    @endif
                                </select>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </form>

        <x-slot name="footer">
            <x-button variant="outline" onclick="closeModal('modalEditDataSatuan')" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
            <x-button variant="primary" type="submit" form="formEditDataSatuan" class="!px-6 !py-2.5 !rounded-lg !bg-amber-500 hover:!bg-amber-600 border-none">Simpan Perubahan</x-button>
        </x-slot>
    </x-modal>

    <!-- Modal Impor Excel -->
    <x-modal id="modalImportExcel" title="Impor Data Surat via Excel" description="Unduh template Excel yang disediakan, isi data satuan, lalu unggah kembali di sini. Sistem akan melewati data duplikat secara otomatis.">
        <form action="{{ route('surat.import.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
        <!-- Download Template injected -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="{{ route('template.download', 'surat') }}" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>

            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs text-gray-600 flex flex-col gap-3 shadow-inner mb-4">
                <div class="flex items-center gap-2 text-green-700 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Instruksi Impor
                </div>
                <ul class="list-disc list-inside pl-1 space-y-1">
                    <li>Gunakan format tanggal di Excel: <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-gray-200">YYYY-MM-DD</span>.</li>
                    <li>Kolom 'Status': <span class="font-bold">Terkirim</span> / <span class="font-bold">Dibatalkan</span>.</li>
                    <li>Kolom 'Jenis Surat': <span class="font-bold">Surat Masuk</span> / <span class="font-bold">Surat Keluar</span>.</li>
                </ul>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih File Excel (.xlsx / .xls) <span class="text-red-500">*</span></label>
                <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
            </div>
            
            <div class="flex justify-end gap-3 mt-5 pt-4 border-t border-gray-100">
                <x-button variant="outline" type="button" onclick="closeModal('modalImportExcel')">Batal</x-button>
                <x-button variant="primary" type="submit" class="!bg-green-600 hover:!bg-green-700 border-none !px-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Unggah & Proses
                </x-button>
            </div>
        </form>
    </x-modal>

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // ================= DROPDOWN TOGGLE LOGIC =================
    function toggleDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) el.classList.remove('hidden');
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    function triggerDeleteKolom(deleteUrl) {
        closeModal('modalAturKolom');
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
    }
    
    function toggleDropdownConfig() {
        const selector = document.getElementById('tipeInputSelector');
        const configArea = document.getElementById('dropdownConfigArea');
        if(selector.value === 'dropdown') {
            configArea.classList.remove('hidden'); configArea.querySelector('input').setAttribute('required', 'true');
        } else {
            configArea.classList.add('hidden'); configArea.querySelector('input').removeAttribute('required');
        }
    }

    function openModalTambah() {
        document.getElementById('formTambah').reset();
        const now = new Date();
        const today = now.toISOString().substring(0, 10);
        document.querySelector('#formTambah input[name="tanggal_surat"]').value = today;
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('formTambah').querySelector('input[name="periode_laporan"]').value = currentMonth;

        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambah');
    }

    function openEditDataSatuanModal(row) {
        document.getElementById('formEditDataSatuan').reset();
        document.getElementById('formEditDataSatuan').action = "/surat/" + row.id;

        document.getElementById('edit_tanggal').value = row.tanggal_surat.substring(0, 10);
        document.getElementById('edit_nomor').value = row.nomor_surat;
        document.getElementById('edit_judul').value = row.judul_surat;
        document.getElementById('edit_drafter').value = row.drafter || '';
        document.getElementById('edit_status').value = row.status;
        document.getElementById('edit_jenis').value = row.jenis_surat;

        const masterMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const monthIndex = masterMonths.indexOf(row.bulan) + 1;
        const formattedMonth = String(monthIndex).padStart(2, '0');
        document.getElementById('edit_periode_laporan').value = `${row.tahun}-${formattedMonth}`;

        const tambahan = row.data_tambahan || {};
        document.querySelectorAll('.input-dinamis-edit').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = tambahan[key] || '';
        });

        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalEditDataSatuan');
    }

    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-currency')) {
            let rawValue = e.target.value.replace(/[^0-9]/g, '');
            if(e.target.nextElementSibling && e.target.nextElementSibling.tagName === 'INPUT') {
                e.target.nextElementSibling.value = rawValue;
            }
            if (rawValue) e.target.value = new Intl.NumberFormat('id-ID').format(rawValue);
            else e.target.value = '';
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.target && e.target.type === 'number') {
            if (['e', 'E', '+', '-', '.'].includes(e.key)) { e.preventDefault(); }
        }
    });

    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500'); field.classList.remove('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500'); field.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
            if (!isValid) e.preventDefault();
        });
    });

    // ================= INTEGRASI DYNAMIC CHART KOMPONEN =================
    const allChartData = {
        chartSurat: {
            labels: {!! json_encode(array_column($chartData, 'label')) !!},
            dataMasuk: {!! json_encode(array_column($chartData, 'masuk')) !!},
            dataKeluar: {!! json_encode(array_column($chartData, 'keluar')) !!},
            hasData: true
        }
    };
    
    let chartInstances = {};

    function renderChart(chartId, type) {
        const canvasEl = document.getElementById('canvas_' + chartId);
        const config = allChartData[chartId];
        if (!canvasEl || !config) return;

        if (chartInstances[chartId]) chartInstances[chartId].destroy();

        // Pengaturan format tooltip dan sumbu yang rapi
        const options = {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: type !== 'bar', position: 'bottom' }, 
                tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8 }
            }
        };

        if (type === 'bar') {
            options.scales = {
                y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            };
        }

        chartInstances[chartId] = new Chart(canvasEl.getContext('2d'), {
            type: type,
            data: {
                labels: config.labels,
                datasets: [
                    { label: 'Surat Masuk', data: config.dataMasuk, backgroundColor: '#0056A3', borderRadius: (type === 'bar' ? 4 : 0) },
                    { label: 'Surat Keluar', data: config.dataKeluar, backgroundColor: '#F7941E', borderRadius: (type === 'bar' ? 4 : 0) }
                ]
            },
            options: options
        });
    }

    // Fungsi ini dipanggil dari event onchange select HTML komponen x-dynamic-chart
    function changeChartType(chartId, newType) { renderChart(chartId, newType); }
    
    // Inisialisasi awal saat dimuat
    window.onload = function () { renderChart('chartSurat', 'bar'); };
</script>
@endsection
