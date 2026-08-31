@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <x-success-modal />

    <!-- ================= MODAL ERROR KUSTOM ================= -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Terjadi Kesalahan</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Tutup
            </button>
        </div>
    </div>
    @endif

    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a><span class="text-gray-400">/</span><span class="text-blue-600 font-medium">Ketidakhadiran</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Ketidakhadiran</h2>
            <p class="text-sm text-gray-500">{{ $tanggalToday }}</p>
        </div>

        <!-- Filter Sederhana tanpa tombol samping -->
        <form action="{{ route('ketidakhadiran.index') }}" method="GET" class="flex items-center gap-3">
            @if(request()->has('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                <option value="semua" {{ $tahun === 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach ($tahunList as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                <option value="semua" {{ $bulanNama == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach ($bulanList as $b)
                    <option value="{{ $b }}" {{ $bulanNama === $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div id="filterChartBanner" class="hidden mb-3 flex items-center justify-between bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5">
        <span class="text-sm text-blue-900">Menampilkan chart untuk: <strong id="filterChartNama"></strong></span>
        <button type="button" onclick="resetFilterKetidakhadiran()" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua Karyawan &times;</button>
    </div>

    <!-- Perbaikan Tinggi Chart Container (diperbesar ke 420px agar Legend masuk semua) -->
    @php
        if ($bulanNama === 'semua' && $tahun === 'semua') {
            $chartSubtitle = 'Akumulasi Keseluruhan - Semua Tahun & Bulan';
        } else {
            $teksWaktu = $bulanNama === 'semua' ? 'Setahun Penuh' : 'Bulan ' . $bulanNama;
            $teksTahun = $tahun === 'semua' ? 'Semua Tahun' : 'Tahun ' . $tahun;
            $chartSubtitle = "Akumulasi $teksWaktu - $teksTahun";
        }
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="h-[420px]"><x-dynamic-chart id="chartPersentase" title="Persentase Ketidakhadiran Karyawan (Hari)" subtitle="{{ $chartSubtitle }}" type="pie"></x-dynamic-chart></div>
        <div class="h-[420px]"><x-dynamic-chart id="chartJumlah" title="Jumlah Ketidakhadiran Karyawan (Hari)" subtitle="{{ $chartSubtitle }}" type="bar"></x-dynamic-chart></div>
    </div>

    @if ($totalHari === 0)
    <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-between">
        <span class="text-sm font-medium text-blue-900">Belum ada data ketidakhadiran untuk {{ $tahun === 'semua' ? 'Semua Tahun' : 'Tahun ' . $tahun }}.</span>
    </div>
    @endif

    <!-- ================= ACTION BAR MINIMALIS ================= -->
    <div class="flex justify-end items-center mb-5 flex-wrap gap-4">
        
        <!-- Opsi Lanjutan (Di sebelah kiri Tambah) -->
        <div class="relative inline-block text-left">
            <button type="button" onclick="toggleDropdown('dropdownOpsiSuper')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Opsi Lanjutan
                <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <!-- Isi Dropdown -->
            <div id="dropdownOpsiSuper" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                
                <!-- Kategori 1: Kelola Data -->
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p>
                </div>
                <div class="py-1" role="none">
                    <button type="button" onclick="openModal('modalImportKetidakhadiran'); toggleDropdown('dropdownOpsiSuper')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Excel
                    </button>
                    <a href="{{ route('ketidakhadiran.export.excel', ['tahun' => $tahun, 'bulan' => $bulanNama]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('ketidakhadiran.export.pdf', ['tahun' => $tahun, 'bulan' => $bulanNama]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export PDF
                    </a>
                </div>

                <!-- Kategori 2: Atur Kolom -->
                <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                </div>
                <div class="py-1" role="none">
                    <button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsiSuper')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Harian
                    </button>
                </div>
            </div>
        </div>

        <!-- Tambah Data Utama (Paling Kanan / Ujung) -->
        <div>
            <button type="button" onclick="bukaModalTambah()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga hover:bg-orange-600 rounded-xl shadow-sm transition-colors border-none outline-none focus:ring-2 focus:ring-orange-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Data
            </button>
        </div>

    </div>
    <!-- ========================================================================= -->

    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100">
        <x-table :headers="['Tahun', 'Bulan', 'Nama', 'NPK', 'Keterangan', 'Dinas', 'Cuti', 'Izin', 'Training', 'Dispensasi', 'Detasering', 'Aksi']">
            @forelse ($karyawan as $item)
                <tr class="row-ketidakhadiran hover:bg-gray-50 transition-colors cursor-pointer" data-nama="{{ $item->nama }}" data-dinas="{{ $item->dinas ?? 0 }}" data-cuti="{{ $item->cuti ?? 0 }}" data-izin="{{ $item->izin ?? 0 }}" data-training="{{ $item->training ?? 0 }}" data-dispensasi="{{ $item->dispensasi ?? 0 }}" data-detasering="{{ $item->detasering ?? 0 }}">
                    <td class="px-6 py-4 text-gray-700 font-medium">{{ $item->tahun }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->bulan }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama }}</td>
                    <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $item->npk }}</td>
                    <td class="px-6 py-4 text-gray-500 truncate max-w-[160px]" title="{{ $item->keterangan }}">{{ $item->keterangan ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->dinas ? 'opacity-40' : '' }}">{{ $item->dinas ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->cuti ? 'opacity-40' : '' }}">{{ $item->cuti ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->izin ? 'opacity-40' : '' }}">{{ $item->izin ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->training ? 'opacity-40' : '' }}">{{ $item->training ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->dispensasi ? 'opacity-40' : '' }}">{{ $item->dispensasi ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->detasering ? 'opacity-40' : '' }}">{{ $item->detasering ?? 0 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button type="button" class="btn-edit-bulanan p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" data-karyawan-id="{{ $item->karyawan_id }}" data-npk="{{ $item->npk }}" data-tahun="{{ $item->tahun }}" data-bulan="{{ $item->bulan }}" data-keterangan="{{ $item->keterangan }}" data-dinas="{{ $item->dinas }}" data-cuti="{{ $item->cuti }}" data-izin="{{ $item->izin }}" data-training="{{ $item->training }}" data-dispensasi="{{ $item->dispensasi }}" data-detasering="{{ $item->detasering }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                            <button type="button" onclick="openDeleteModal('modalHapusBulanan', '{{ route('ketidakhadiran.destroyBulanan', $item->ketidakhadiran_id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            <a href="{{ route('ketidakhadiran.harian', ['karyawan_id' => $item->karyawan_id, 'tahun' => $item->tahun, 'bulan' => $item->bulan]) }}" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-md border border-blue-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="12" class="px-6 py-4 text-center text-gray-500">Belum ada data karyawan.</td></tr>
            @endforelse
        </x-table>
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            <div>@if ($karyawan->total() > 0) Menampilkan {{ $karyawan->firstItem() }}–{{ $karyawan->lastItem() }} dari {{ $karyawan->total() }} karyawan @else Tidak ada karyawan ditemukan @endif</div>
            <div>{{ $karyawan->links() }}</div>
        </div>
    </x-card>

    <x-delete-modal id="modalHapusBulanan" title="Hapus Data Bulanan" message="Apakah Anda yakin ingin menghapus seluruh data ketidakhadiran karyawan ini untuk bulan yang dipilih?" />
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel harian. Lanjutkan?" />

    <!-- ================= MODAL ATUR KOLOM DINAMIS ================= -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan (Harian)" description="Kelola kolom ekstra khusus untuk formulir Ketidakhadiran Harian.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar Saat Ini:</h4>
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown)) }} @endif
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
            <input type="hidden" name="modul" value="ketidakhadiran_harian">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Buat Kolom Baru:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kolom (Cth: Biaya RS)</label>
                    <input type="text" name="nama_kolom" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
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
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Ringan, Berat" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi jika memilih dropdown!</span>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-5">
                <x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button>
                <x-button variant="primary" type="submit" class="!bg-blue-600 hover:!bg-blue-700 border-none">Simpan Kolom</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH / EDIT DATA ================= -->
    <x-modal id="modalTambahData" title="Tambah / Edit Data Ketidakhadiran" description="Pilih input per hari, atau langsung isi total 1 bulan sekaligus">
        <div class="flex gap-2 mb-5 border-b border-gray-100">
            <button type="button" id="tabHarianBtn" onclick="switchTabInput('harian')" class="px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 transition-colors">Input Harian</button>
            <button type="button" id="tabBulananBtn" onclick="switchTabInput('bulanan')" class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 transition-colors">Input / Edit Bulanan</button>
        </div>

        <!-- FORM HARIAN -->
        <form action="{{ route('ketidakhadiran.storeHarian') }}" method="POST" id="formHarian" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required value="{{ now()->toDateString() }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
            </div>
            <!-- SPACING FOR GRID LAYOUT (to push the next item to a new line if needed, or just let it flow) -->
            <div class="hidden md:block"></div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Karyawan <span class="text-red-500">*</span></label>
                <select name="karyawan_id" required onchange="autofillNpk(this, 'npkHarian')" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Pilih karyawan...</option>
                    @foreach ($daftarKaryawan as $k)
                        <option value="{{ $k->id }}" data-npk="{{ $k->npk }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NPK</label>
                <input type="text" id="npkHarian" readonly placeholder="Otomatis terisi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Ketidakhadiran <span class="text-red-500">*</span></label>
                <select name="jenis" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Pilih jenis...</option>
                    <option value="dinas">Dinas</option><option value="cuti">Cuti</option>
                    <option value="izin">Izin</option><option value="training">Training</option>
                    <option value="dispensasi">Dispensasi</option><option value="detasering">Detasering</option>
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                <p class="text-[10px] text-gray-400 mt-1.5">Maksimal 1 jenis per hari per karyawan. Kalau tanggal ini sudah pernah diisi, jenisnya akan diganti otomatis.</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan (opsional)</label>
                <textarea name="keterangan" rows="2" placeholder="cth. Dinas ke Jakarta" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
            </div>

            <!-- AREA KOLOM DINAMIS (HARIAN) -->
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'text')
                            <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'number')
                            <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">Rp</span></div>
                                <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-currency w-full pl-9 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none" placeholder="0">
                            </div>
                        @elseif($kolom->tipe_input === 'date')
                            <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
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
        </form>

        <!-- FORM BULANAN (Statis - Tidak diinject kolom dinamis karena ini agregasi) -->
        <form action="{{ route('ketidakhadiran.storeBulanan') }}" method="POST" id="formBulanan" class="hidden grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan & Tahun <span class="text-red-500">*</span></label>
                @php
                    // Set default to selected month/year if they are valid, else current month
                    $defaultYear = $tahun !== 'semua' ? $tahun : now()->year;
                    $defaultMonthNum = 1;
                    if ($bulanNama !== 'semua') {
                        $idx = array_search($bulanNama, $bulanList);
                        if ($idx !== false) $defaultMonthNum = $idx + 1;
                    } else {
                        $defaultMonthNum = now()->month;
                    }
                    $defaultVal = sprintf('%04d-%02d', $defaultYear, $defaultMonthNum);
                @endphp
                <input type="month" name="bulan_tahun" id="bulanTahunBulanan" required value="{{ $defaultVal }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
            </div>
            <div class="hidden md:block"></div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Karyawan <span class="text-red-500">*</span></label>
                <select name="karyawan_id" id="karyawanBulanan" required onchange="autofillNpk(this, 'npkBulanan')" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Pilih karyawan...</option>
                    @foreach ($daftarKaryawan as $k)
                        <option value="{{ $k->id }}" data-npk="{{ $k->npk }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NPK</label>
                <input type="text" id="npkBulanan" readonly placeholder="Otomatis terisi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan (opsional)</label>
                <textarea name="keterangan" id="keteranganBulanan" rows="2" placeholder="cth. Cuti melahirkan" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dinas</label>
                <input type="text" name="dinas" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cuti</label>
                <input type="text" name="cuti" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Izin</label>
                <input type="text" name="izin" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Training</label>
                <input type="text" name="training" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dispensasi</label>
                <input type="text" name="dispensasi" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Detasering</label>
                <input type="text" name="detasering" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <p class="md:col-span-2 text-xs text-gray-400">Menyimpan form ini akan MENIMPA angka yang sudah ada untuk karyawan & bulan yang sama.</p>
        </form>

        <x-slot name="footer">
            <x-button variant="outline" type="button" onclick="document.getElementById('modalTambahData').classList.add('hidden')" class="!px-6 !py-2.5 !rounded-lg">Batal</x-button>
            <x-button variant="secondary" type="submit" id="btnSubmitTambah" form="formHarian" class="!px-6 !py-2.5 !rounded-lg border-none shadow-md">Simpan Data</x-button>
        </x-slot>
    </x-modal>

    <!-- MODAL IMPORT KETIDAKHADIRAN -->
    <x-import-modal 
        id="modalImportKetidakhadiran" 
        route="{{ route('ketidakhadiran.import') }}" 
        title="Import Data Ketidakhadiran Bulanan" 
        templateRoute="{{ route('template.download', 'ketidakhadiran') }}" 
    />

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // --- DROPDOWN TOGGLE LOGIC ---
    function toggleDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        
        // Tutup semua dropdown lain terlebih dahulu
        document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        
        // Buka jika sebelumnya tersembunyi
        if (isHidden) {
            el.classList.remove('hidden');
        }
    }

    // Menutup dropdown jika klik di luar area
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    // MENCEGAH BENTROK MODAL
    function triggerDeleteKolom(deleteUrl) {
        closeModal('modalAturKolom');
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
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
            configArea.querySelector('input').value = '';
        }
    }

    // AUTO FORMAT CURRENCY
    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-currency')) {
            let value = e.target.value.replace(/[^,\d]/g, '');
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if(ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            e.target.value = rupiah;
        }
    });

    // AUTO-OPEN MODAL ERROR PENGATURAN KOLOM
    @if($errors->has('nama_kolom') || $errors->has('tipe_input'))
        document.addEventListener('DOMContentLoaded', function() {
            openModal('modalAturKolom');
        });
    @endif

    // VALIDASI MERAH FORM
    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500', 'bg-red-50'); 
                    field.classList.remove('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden'); 
                } else {
                    field.classList.remove('border-red-500', 'bg-red-50');
                    field.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden'); 
                }
            });
            if (!isValid) e.preventDefault(); 
        });
        
        form.querySelectorAll('[required]').forEach(field => {
            field.addEventListener(field.tagName === 'SELECT' ? 'change' : 'input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    this.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                } else {
                    this.classList.add('border-red-500', 'bg-red-50');
                    this.classList.remove('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                }
            });
        });
    });

    // Modal & Tabs Logic
    function bukaModalTambah() {
        document.getElementById('formBulanan').reset();
        document.getElementById('formHarian').reset();
        document.getElementById('npkHarian').value = '';
        document.getElementById('npkBulanan').value = '';
        
        const tInput = document.getElementById('bulanTahunBulanan');
        if (tInput) {
            tInput.removeAttribute('readonly');
            tInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
        }

        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        document.getElementById('modalTambahData').classList.remove('hidden');
        switchTabInput('harian');
    }

    function bukaEditBulanan(data) {
        document.getElementById('modalTambahData').classList.remove('hidden');
        switchTabInput('bulanan');

        const formBulanan = document.getElementById('formBulanan');
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        formBulanan.querySelector('[name="karyawan_id"]').value = data.karyawanId;
        
        const tInput = document.getElementById('bulanTahunBulanan');
        if (tInput) {
            const bulanNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const mIdx = bulanNames.indexOf(data.bulan) + 1;
            const monthNumStr = mIdx < 10 ? '0' + mIdx : mIdx;
            tInput.value = data.tahun + '-' + monthNumStr;
            tInput.setAttribute('readonly', true);
            tInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        }

        document.getElementById('npkBulanan').value = data.npk ?? '';
        document.getElementById('keteranganBulanan').value = data.keterangan ?? '';

        ['dinas', 'cuti', 'izin', 'training', 'dispensasi', 'detasering'].forEach(function (kategori) {
            formBulanan.querySelector('[name="' + kategori + '"]').value = data[kategori] ?? 0;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-edit-bulanan').forEach(function (btn) {
            btn.addEventListener('click', function () { bukaEditBulanan(this.dataset); });
        });

        document.querySelectorAll('.row-ketidakhadiran').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.closest('a, button')) return;
                pilihBarisKetidakhadiran(row);
            });
        });
    });

    function switchTabInput(tab) {
        const formHarian = document.getElementById('formHarian');
        const formBulanan = document.getElementById('formBulanan');
        const tabHarianBtn = document.getElementById('tabHarianBtn');
        const tabBulananBtn = document.getElementById('tabBulananBtn');
        const btnSubmit = document.getElementById('btnSubmitTambah');

        const aktif = ['border-blue-600', 'text-blue-600'];
        const nonAktif = ['border-transparent', 'text-gray-400'];

        if (tab === 'harian') {
            formHarian.classList.remove('hidden'); formHarian.classList.add('grid');
            formBulanan.classList.add('hidden'); formBulanan.classList.remove('grid');
            tabHarianBtn.classList.add(...aktif); tabHarianBtn.classList.remove(...nonAktif);
            tabBulananBtn.classList.add(...nonAktif); tabBulananBtn.classList.remove(...aktif);
            btnSubmit.setAttribute('form', 'formHarian');
        } else {
            formBulanan.classList.remove('hidden'); formBulanan.classList.add('grid');
            formHarian.classList.add('hidden'); formHarian.classList.remove('grid');
            tabBulananBtn.classList.add(...aktif); tabBulananBtn.classList.remove(...nonAktif);
            tabHarianBtn.classList.add(...nonAktif); tabHarianBtn.classList.remove(...aktif);
            btnSubmit.setAttribute('form', 'formBulanan');
        }
    }

    function autofillNpk(selectEl, targetInputId) {
        const opt = selectEl.options[selectEl.selectedIndex];
        document.getElementById(targetInputId).value = opt ? (opt.getAttribute('data-npk') || '') : '';
    }

    // Chart.js Setup
    Chart.register(ChartDataLabels);

    const totalHariPHP = {{ $totalHari }};
    const kategoriLabels = {!! json_encode(array_keys($totalPerKategori)) !!};
    const kategoriValues = {!! json_encode(array_values($totalPerKategori)) !!};
    const paletteKategori = ['#3B82F6', '#F97316', '#10B981', '#A855F7', '#EF4444', '#FACC15'];

    const dataPersentase = totalHariPHP > 0 ? { labels: kategoriLabels, datasets: [{ data: kategoriValues, backgroundColor: paletteKategori, borderWidth: 0 }] } : { labels: ['Belum ada data'], datasets: [{ data: [1], backgroundColor: ['#E5E7EB'], borderWidth: 0 }] };
    const dataJumlah = { labels: kategoriLabels, datasets: [{ data: kategoriValues, backgroundColor: paletteKategori, borderWidth: 0 }] };

    let currentDataPersentase = dataPersentase;
    let currentDataJumlah = dataJumlah;
    let currentTotalHari = totalHariPHP;
    let chartTypeState = { chartPersentase: 'pie', chartJumlah: 'bar' };

    // TAMBAHAN UNTUK MEMPERBAIKI COMPONENT DYNAMIC CHART
    function changeChartType(chartId, newType) {
        chartTypeState[chartId] = newType;
        renderChartsKetidakhadiran();
    }

    function buildPluginsConfig(type, chartId) {
        return {
            legend: { 
                display: type !== 'bar', 
                position: 'bottom', 
                labels: { 
                    usePointStyle: true, 
                    padding: 12, // Perbaikan Padding agar lebih rapat
                    font: { size: 11, weight: '500' } 
                } 
            },
            tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8, titleFont: { size: 14, weight: '700' }, bodyFont: { size: 12 },
                callbacks: {
                    label: function (context) {
                        if (currentTotalHari === 0) return 'Belum ada data ketidakhadiran';
                        const label = context.label || '';
                        const value = context.parsed.y !== undefined ? context.parsed.y : context.parsed;
                        if (chartId === 'chartPersentase') {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${percentage}% (${value} hari)`;
                        }
                        return `${label}: ${value} hari`;
                    }
                }
            },
            datalabels: {
                display: type === 'bar' ? false : function (context) {
                    if (currentTotalHari === 0) return false;
                    return context.dataset.data[context.dataIndex] > 0;
                },
                color: '#fff', font: { weight: 'bold', size: 16 },
                formatter: function (value, context) { 
                    if (chartId === 'chartPersentase') {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        if (total === 0) return '';
                        return Math.round((value / total) * 100) + '%';
                    }
                    return value; 
                },
                anchor: 'center', align: 'center'
            }
        };
    }

    // Perbaikan: Parameter maintainAspectRatio diset "false" agar mengikuti box tinggi (h-[420px])
    function renderChartsKetidakhadiran() {
        createChart('chartPersentase', chartTypeState.chartPersentase, currentDataPersentase, false);
        createChart('chartJumlah', chartTypeState.chartJumlah, currentDataJumlah, false);
    }

    function tampilkanBannerFilter(nama) {
        const banner = document.getElementById('filterChartBanner');
        const namaEl = document.getElementById('filterChartNama');
        if (banner && namaEl) { namaEl.textContent = nama; banner.classList.remove('hidden'); }
    }

    function sembunyikanBannerFilter() {
        const banner = document.getElementById('filterChartBanner');
        if (banner) banner.classList.add('hidden');
    }

    const kategoriKeyList = ['dinas', 'cuti', 'izin', 'training', 'dispensasi', 'detasering'];
    function pilihBarisKetidakhadiran(row) {
        document.querySelectorAll('.row-ketidakhadiran').forEach(function (r) {
            r.classList.remove('bg-blue-50', 'ring-1', 'ring-inset', 'ring-blue-300');
            r.classList.add('opacity-30');
        });
        row.classList.remove('opacity-30');
        row.classList.add('bg-blue-50', 'ring-1', 'ring-inset', 'ring-blue-300');

        const nilai = kategoriKeyList.map(function (key) { return parseInt(row.dataset[key] || '0', 10); });
        const totalBaris = nilai.reduce(function (a, b) { return a + b; }, 0);
        currentTotalHari = totalBaris;

        currentDataPersentase = totalBaris > 0 ? { labels: kategoriLabels, datasets: [{ data: nilai, backgroundColor: paletteKategori, borderWidth: 0 }] } : { labels: ['Belum ada data'], datasets: [{ data: [1], backgroundColor: ['#E5E7EB'], borderWidth: 0 }] };
        currentDataJumlah = { labels: kategoriLabels, datasets: [{ data: nilai, backgroundColor: paletteKategori, borderWidth: 0 }] };

        renderChartsKetidakhadiran();
        tampilkanBannerFilter(row.dataset.nama);
    }

    function resetFilterKetidakhadiran() {
        document.querySelectorAll('.row-ketidakhadiran').forEach(function (r) { r.classList.remove('opacity-30', 'bg-blue-50', 'ring-1', 'ring-inset', 'ring-blue-300'); });
        currentDataPersentase = dataPersentase;
        currentDataJumlah = dataJumlah;
        currentTotalHari = totalHariPHP;
        renderChartsKetidakhadiran();
        sembunyikanBannerFilter();
    }

    window.charts = {};

    function createChart(chartId, type, data, maintainAspectRatio = true) {
        const ctx = document.getElementById('canvas_' + chartId).getContext('2d');
        if (window.charts[chartId]) window.charts[chartId].destroy();

        const chartOptions = { responsive: true, maintainAspectRatio: maintainAspectRatio, plugins: buildPluginsConfig(type, chartId) };
        if (type === 'bar') { chartOptions.scales = { y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10, weight: '500' } } } }; }

        window.charts[chartId] = new Chart(ctx, { type: type, data: data, options: chartOptions });
    }

    window.onload = function () { renderChartsKetidakhadiran(); };
</script>
@endsection
