@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    <!-- ================= MODAL ERROR KUSTOM ================= -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 mb-2">Penambahan Gagal</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>

            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none'; openModal('modalAturKolom');" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Kembali & Perbaiki
            </button>
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
                <span class="text-blue-600 font-medium">Pelaporan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pelaporan Korporat</h2>
            <p class="text-sm text-gray-500 font-medium">{{ \Carbon\Carbon::now()->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
        
        <form action="{{ route('pelaporan.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-orange-500">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-orange-500">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART SECTION ================= -->
    <x-card class="!rounded-xl overflow-hidden shadow-sm border border-gray-100 mb-8 p-6 bg-white">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Statistik Volume Laporan {{ $filterTahun == 'semua' ? 'Tahunan' : 'Bulanan' }}</h3>
                <p class="text-xs text-gray-400">Distribusi jumlah pelaporan Eksternal vs Internal berdasarkan filter</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div class="flex items-center gap-1.5">
                    <span class="w-3.5 h-3.5 rounded-md bg-[#F7941E]"></span>
                    <span class="text-gray-700 font-medium">Laporan Eksternal</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3.5 h-3.5 rounded-md bg-[#0056A3]"></span>
                    <span class="text-gray-700 font-medium">Laporan Internal</span>
                </div>
            </div>
        </div>

        <div class="h-64 w-full relative">
            <canvas id="pelaporanChart"></canvas>
        </div>
    </x-card>

    <!-- ================= TABEL 1: RINGKASAN AKUMULASI ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Ringkasan Akumulasi Laporan</h3>
                <p class="text-xs text-gray-400">Total data laporan per periode</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['Tahun', 'Bulan', 'Tujuan Eksternal', 'Tujuan Internal', 'Total Laporan']">
                @forelse($dataRingkasan as $index => $row)
                    <tr class="ringkasan-row hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $row['eksternal'] }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $row['internal'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-bold text-center">{{ $row['total_laporan'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">Tidak ada data ringkasan.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
        
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            <span id="ringkasanPageInfo">Menampilkan data</span>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="ringkasanChangePage(-1)" class="p-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" onclick="ringkasanChangePage(1)" class="p-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PELAPORAN ================= -->
    <x-card class="!rounded-xl !p-0 shadow-sm border border-gray-100 bg-white mb-8 relative">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 rounded-t-xl">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Rincian Pelaporan</h3>
                <p class="text-xs text-gray-400">Detail spesifik masing-masing laporan</p>
            </div>
            
            <!-- ACTION BAR MINIMALIS -->
            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperPelaporan')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdownOpsiSuperPelaporan" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalImportPelaporan'); toggleActionDropdown('dropdownOpsiSuperPelaporan')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Excel
                            </button>
                            <a href="{{ route('pelaporan.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export Excel
                            </a>
                            <a href="{{ route('pelaporan.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Export PDF
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolom'); toggleActionDropdown('dropdownOpsiSuperPelaporan')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
                    </div>
                </div>

                <x-button variant="primary" onclick="openModalTambah()" class="!py-2 text-xs !bg-[#F7941E] hover:!bg-orange-600 border-none !rounded-xl shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Laporan
                </x-button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <!-- HEADER TABEL DINAMIS -->
            @php
                $tableHeaders = ['Tujuan Laporan', 'Nomor Laporan', 'Laporan', 'Tanggal', 'Jenis'];
                if(isset($kolomDinamis)) {
                    foreach($kolomDinamis as $k) {
                        $tableHeaders[] = $k->nama_kolom;
                    }
                }
                $tableHeaders[] = 'Aksi';
            @endphp

            <x-table :headers="$tableHeaders">
                @forelse($dataRincian as $index => $item)
                    @php
                        $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []);
                    @endphp

                    <tr class="pelaporan-row hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-center">
                            @if($item->tujuan == 'Eksternal')
                                <span class="bg-orange-100 text-orange-700 px-2.5 py-1 rounded-md font-medium text-[10px]">Eksternal</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-md font-medium text-[10px]">Internal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $item->nomor }}</td>
                        <td class="px-4 py-3 text-gray-600 truncate max-w-xs text-center" title="{{ $item->laporan }}">{{ $item->laporan }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $item->jenis }}</td>
                        
                        <!-- ISI KOLOM DINAMIS -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-4 py-3 text-gray-600 text-center font-medium">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                        Rp {{ $tambahan[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editLaporan({{ json_encode($item) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusLaporan', '{{ route('pelaporan.destroy', $item->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-10 text-center text-gray-500 text-sm">Tidak ada rincian pelaporan.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
        
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            <div>
                @if ($dataRincian->total() > 0)
                    Menampilkan {{ $dataRincian->firstItem() }}–{{ $dataRincian->lastItem() }} dari {{ $dataRincian->total() }} laporan
                @else
                    Tidak ada laporan ditemukan
                @endif
            </div>
            <div>{{ $dataRincian->links() }}</div>
        </div>
    </x-card>

    <!-- Modal Konfirmasi Hapus Data Pelaporan & Kolom -->
    <x-delete-modal id="modalHapusLaporan" title="Hapus Data Laporan" message="Data laporan ini akan dihapus secara permanen dari sistem. Lanjutkan?" />
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel dan formulir. Data yang sudah tersimpan sebelumnya tidak akan terhapus dari database. Lanjutkan?" />

    <!-- MODAL IMPORT PELAPORAN -->
    <x-import-modal 
        id="modalImportPelaporan" 
        route="{{ route('pelaporan.import') }}" 
        title="Import Data Pelaporan" 
        templateRoute="{{ route('pelaporan.template.excel') }}" 
    />

    <!-- ================= MODAL ATUR KOLOM DINAMIS ================= -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan" description="Kelola kolom ekstra khusus untuk formulir Pelaporan.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar Saat Ini:</h4>
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) 
                                    | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown)) }} 
                                @endif
                            </p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded transition-colors" title="Hapus Kolom">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach
            @else
                <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan yang dibuat.</p>
            @endif
        </div>

        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t border-gray-200 pt-5">
            @csrf
            <input type="hidden" name="modul" value="pelaporan">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Buat Kolom Baru:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kolom (Cth: Harga Sewa)</label>
                    <input type="text" name="nama_kolom" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option>
                        <option value="number">Angka Kuantitas Biasa</option>
                        <option value="currency">Harga / Uang (Titik Otomatis)</option>
                        <option value="date">Tanggal</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Tinggi, Sedang, Rendah" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-5">
                <x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button>
                <x-button variant="primary" type="submit" class="!bg-blue-600 hover:!bg-blue-700 border-none">Simpan Kolom</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH LAPORAN ================= -->
    <x-modal id="modalTambahLaporan" title="Formulir Tambah Laporan" description="Masukkan detail pelaporan yang baru.">
        <form action="{{ route('pelaporan.store') }}" method="POST" id="formTambahLaporan" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            
            <!-- FIELD STANDAR -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tujuan Laporan <span class="text-red-500">*</span></label>
                <select name="tujuan" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <option value="" disabled selected>Pilih Tujuan...</option>
                    <option value="Eksternal">Eksternal</option>
                    <option value="Internal">Internal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Laporan <span class="text-red-500">*</span></label>
                <select name="jenis" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <option value="" disabled selected>Pilih Jenis...</option>
                    <option value="Bulanan">Bulanan</option>
                    <option value="Triwulan">Triwulan</option>
                    <option value="Semester">Semester</option>
                    <option value="Tahunan">Tahunan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor <span class="text-red-500">*</span></label>
                <input type="text" name="nomor" required placeholder="Cth: LAP/01" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Laporan <span class="text-red-500">*</span></label>
                <textarea name="laporan" rows="2" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
            </div>

            <!-- AREA KOLOM DINAMIS (TAMBAH) -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'text')
                            <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'number')
                            <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">Rp</span></div>
                                <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-currency w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none" placeholder="0">
                            </div>
                        @elseif($kolom->tipe_input === 'date')
                            <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
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
            @endif

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahLaporan')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL EDIT LAPORAN ================= -->
    <x-modal id="modalEditLaporan" title="Edit Data Laporan" description="Perbarui rincian laporan yang sudah ada.">
        <form action="" method="POST" id="formEditLaporan" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <!-- FIELD STANDAR -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tujuan Laporan</label>
                <select name="tujuan" id="edit_tujuan" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                    <option value="Eksternal">Eksternal</option><option value="Internal">Internal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis</label>
                <select name="jenis" id="edit_jenis" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
                    <option value="Bulanan">Bulanan</option><option value="Triwulan">Triwulan</option><option value="Semester">Semester</option><option value="Tahunan">Tahunan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal</label>
                <input type="date" name="tanggal" id="edit_tanggal" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor</label>
                <input type="text" name="nomor" id="edit_nomor" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Laporan</label>
                <textarea name="laporan" id="edit_laporan" rows="2" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none"></textarea>
            </div>

            <!-- AREA KOLOM DINAMIS (EDIT) -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'text')
                            <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'number')
                            <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">Rp</span></div>
                                <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-edit w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                            </div>
                        @elseif($kolom->tipe_input === 'date')
                            <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-edit w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
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
            @endif

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEditLaporan')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>
</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function toggleActionDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) { el.classList.remove('hidden'); }
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    function triggerDeleteKolom(deleteUrl) {
        closeModal('modalAturKolom');
        setTimeout(() => {
            openDeleteModal('modalHapusKolom', deleteUrl);
        }, 200);
    }

    function toggleDropdownConfig() {
        const selector = document.getElementById('tipeInputSelector');
        const configArea = document.getElementById('dropdownConfigArea');
        if(selector.value === 'dropdown') {
            configArea.classList.remove('hidden');
            configArea.querySelector('input').setAttribute('required', 'true');
        } else {
            configArea.classList.add('hidden');
            configArea.querySelector('input').removeAttribute('required');
        }
    }

    function openModalTambah() {
        document.getElementById('formTambahLaporan').reset();
        openModal('modalTambahLaporan');
    }

    function editLaporan(data) {
        const form = document.getElementById('formEditLaporan');
        form.action = `/pelaporan/${data.id}`;
        
        document.getElementById('edit_tujuan').value = data.tujuan;
        
        // Logika Pintar: Tambah opsi ke dropdown otomatis jika belum ada!
        const jenisSelect = document.getElementById('edit_jenis');
        let optionExists = false;
        for (let i = 0; i < jenisSelect.options.length; i++) {
            if (jenisSelect.options[i].value === data.jenis) {
                optionExists = true; break;
            }
        }
        if (!optionExists && data.jenis) {
            const newOption = document.createElement("option");
            newOption.text = data.jenis;
            newOption.value = data.jenis;
            jenisSelect.add(newOption);
        }
        jenisSelect.value = data.jenis;

        document.getElementById('edit_tanggal').value = data.tanggal;
        document.getElementById('edit_nomor').value = data.nomor;
        document.getElementById('edit_laporan').value = data.laporan;

        const tambahan = typeof data.data_tambahan === 'string' ? JSON.parse(data.data_tambahan) : (data.data_tambahan || {});
        document.querySelectorAll('.input-dinamis-edit').forEach(el => {
            const key = el.getAttribute('data-key');
            if (tambahan && tambahan[key] !== undefined) {
                el.value = tambahan[key];
            } else {
                el.value = '';
            }
        });

        openModal('modalEditLaporan');
    }

    // ==========================================================
    // AUTO-DOT FORMATTER UNTUK HARGA (MATA UANG)
    // ==========================================================
    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-currency')) {
            let value = e.target.value.replace(/[^,\d]/g, '');
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if(ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            e.target.value = rupiah;
        }
    });

    // ==========================================================
    // CLIENT-SIDE VALIDATION LOGIC
    // ==========================================================
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
                } else {
                    this.classList.add('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                }
            });
        });
    });

    @if($errors->has('nama_kolom') || $errors->has('tipe_input'))
        document.addEventListener('DOMContentLoaded', function() {
            openModal('modalAturKolom');
        });
    @endif

    // ==========================================================
    // PAGINATION LOGIC FOR RINGKASAN
    // ==========================================================
    let ringkasanCurrentPage = 1;
    const ringkasanPerPage = 5;

    function renderRingkasanPagination() {
        const rows = Array.from(document.querySelectorAll('.ringkasan-row'));
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / ringkasanPerPage);
        
        if (totalRows === 0) {
            const info = document.getElementById('ringkasanPageInfo');
            if(info) info.textContent = "Tidak ada data ringkasan.";
            return;
        }

        if (ringkasanCurrentPage < 1) ringkasanCurrentPage = 1;
        if (ringkasanCurrentPage > totalPages) ringkasanCurrentPage = totalPages;

        const startIndex = (ringkasanCurrentPage - 1) * ringkasanPerPage;
        const endIndex = startIndex + ringkasanPerPage;

        rows.forEach((row, index) => {
            if (index >= startIndex && index < endIndex) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const info = document.getElementById('ringkasanPageInfo');
        if(info) {
            info.textContent = `Menampilkan ${startIndex + 1}–${Math.min(endIndex, totalRows)} dari ${totalRows} data`;
        }
    }

    function ringkasanChangePage(direction) {
        const rows = document.querySelectorAll('.ringkasan-row');
        const totalPages = Math.ceil(rows.length / ringkasanPerPage);
        
        ringkasanCurrentPage += direction;
        if (ringkasanCurrentPage < 1) ringkasanCurrentPage = 1;
        if (ringkasanCurrentPage > totalPages) ringkasanCurrentPage = totalPages;
        
        renderRingkasanPagination();
    }

    // ==========================================================
    // SEARCH LOGIC FOR RINCIAN
    // ==========================================================
    function pelaporanSearch(term) {
        if (!term) return;
        term = term.toLowerCase().trim();
        const rows = document.querySelectorAll('.pelaporan-row');
        
        rows.forEach(function(row) {
            // Hapus semua mark highlight sebelumnya
            row.querySelectorAll('mark.pelaporan-highlight').forEach(function(mark) {
                const parent = mark.parentNode;
                parent.replaceChild(document.createTextNode(mark.textContent), mark);
                parent.normalize();
            });

            if (term === '') {
                return;
            }

            if (row.textContent.toLowerCase().includes(term)) {
                row.querySelectorAll('td').forEach(function(td) {
                    const walker = document.createTreeWalker(td, NodeFilter.SHOW_TEXT, null, false);
                    const textNodes = [];
                    let node;
                    while ((node = walker.nextNode())) { textNodes.push(node); }

                    textNodes.forEach(function(textNode) {
                        const content = textNode.nodeValue;
                        if (!content.toLowerCase().includes(term)) return;

                        const escaped = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        const regex = new RegExp(escaped, 'gi');
                        const fragment = document.createDocumentFragment();
                        let lastIndex = 0;
                        let match;

                        regex.lastIndex = 0;
                        while ((match = regex.exec(content)) !== null) {
                            if (match.index > lastIndex) {
                                fragment.appendChild(document.createTextNode(content.slice(lastIndex, match.index)));
                            }
                            const mark = document.createElement('mark');
                            mark.className = 'pelaporan-highlight';
                            mark.style.backgroundColor = 'yellow';
                            mark.style.color = 'black';
                            mark.style.fontWeight = 'bold';
                            mark.style.borderRadius = '2px';
                            mark.style.padding = '1px 2px';
                            mark.textContent = match[0];
                            fragment.appendChild(mark);
                            lastIndex = match.index + match[0].length;
                        }
                        if (lastIndex < content.length) {
                            fragment.appendChild(document.createTextNode(content.slice(lastIndex)));
                        }
                        textNode.parentNode.replaceChild(fragment, textNode);
                    });
                });
            }
        });
    }

    // ==========================================================
    // CHART.JS LOGIC
    // ==========================================================
    document.addEventListener('DOMContentLoaded', function() {
        renderRingkasanPagination();
        
        // Trigger highlight if there is a search query from server
        @if(request('search'))
            pelaporanSearch("{{ request('search') }}");
        @endif
        const canvas = document.getElementById('pelaporanChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const rawChartData = {!! json_encode($chartData ?? []) !!};
            
            const labels = rawChartData.map(d => d.label);
            const dataEksternal = rawChartData.map(d => d.eksternal);
            const dataInternal = rawChartData.map(d => d.internal);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Eksternal', data: dataEksternal, backgroundColor: '#F7941E', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                        { label: 'Internal', data: dataInternal, backgroundColor: '#0056A3', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#F3F4F6' } }, x: { grid: { display: false } } } }
            });
        }
    });
</script>
@endsection