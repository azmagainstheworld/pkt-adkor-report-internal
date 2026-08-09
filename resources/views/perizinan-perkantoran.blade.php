@extends('layouts.app')

@section('content')
{{-- Load library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- Load Flatpickr untuk Year Picker --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <x-success-modal />

    {{-- Notifikasi Error Validasi Backend --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm flex flex-col shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-bold">Gagal menyimpan data. Periksa inputan Anda:</span>
            </div>
            <ul class="list-disc list-inside pl-8 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Perizinan Perkantoran</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Perizinan Perkantoran</h2>
            <p class="text-sm text-gray-500">{{ $tanggalToday }}</p>
        </div>
        
        <!-- Filter Kanan Atas Dinamis -->
        <form action="{{ route('perizinan-perkantoran.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>

            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
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
                <h3 class="font-bold text-gray-900 text-lg">Statistik Perizinan Terbit ({{ $filterTahun == 'semua' ? 'Semua Tahun' : $filterTahun }})</h3>
                <p class="text-xs text-gray-400">Distribusi 5 kategori kegiatan berdasarkan filter periode</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#A855F7]"></span><span class="text-gray-700 font-medium">Produk</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#2563EB]"></span><span class="text-gray-700 font-medium">Aset</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#EF4444]"></span><span class="text-gray-700 font-medium">Proyek</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#22C55E]"></span><span class="text-gray-700 font-medium">Peralatan Pabrik</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-[#F97316]"></span><span class="text-gray-700 font-medium">Adm & Lainnya</span></div>
            </div>
        </div>

        <div class="h-64 w-full relative">
            <canvas id="perizinanChart"></canvas>
        </div>
    </x-card>

    <!-- ================= TABEL 1: RINGKASAN AKUMULASI (SESUAI EXCEL) ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Ringkasan Kegiatan Perizinan Terbit</h3>
            <p class="text-xs text-gray-400">Akumulasi total terbit per kategori kegiatan sesuai periode filter</p>
        </div>

        <x-table :headers="['Tahun', 'Bulan', 'Produk', 'Aset', 'Proyek', 'Peralatan Pabrik', 'Adm & Lainnya', 'Total Perizinan Terbit']">
            @forelse($dataRingkasan as $index => $ringkasan)
                <tr class="{{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }} hover:bg-gray-100 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-700 font-medium text-center align-middle">{{ $ringkasan['tahun'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['bulan'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['produk'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['aset'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['proyek'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['peralatan_pabrik'] }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $ringkasan['adm'] }}</td>
                    <td class="px-6 py-4 bg-blue-50 font-bold text-blue-900 text-center align-middle">{{ $ringkasan['total_terbit'] }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data ringkasan.</td></tr>
            @endforelse
        </x-table>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PERIZINAN (SESUAI EXCEL) ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Perizinan Terbit</h3>
                <p class="text-xs text-gray-400">Detail dokumen perizinan sesuai filter yang dipilih</p>
            </div>
            
            <x-button variant="primary" onclick="openModalTambah()" class="shadow-sm text-xs border-none !py-2">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Rincian Terbit
            </x-button>
        </div>

        <x-table :headers="['NO', 'Tahun', 'Perizinan Terbit', 'Nomor', 'Terbit', 'Berakhir', 'Instansi Penerbit', 'Bulan', 'Kegiatan', 'Aksi']">
            @forelse ($dataRincian as $index => $rincian)
                <tr class="{{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }} hover:bg-gray-100 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-700 font-medium text-center align-middle">{{ $dataRincian->firstItem() + $index }}</td>
                    
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ Carbon\Carbon::parse($rincian->tanggal_sejak)->year }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900 truncate max-w-[200px] text-center align-middle" title="{{ $rincian->nama_perizinan }}">{{ $rincian->nama_perizinan ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 font-mono text-xs text-center align-middle">{{ $rincian->nomor }}</td>
                    {{-- Format Tanggal Indonesia (Contoh: 15 Januari 2024) --}}
                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-center align-middle">{{ Carbon\Carbon::parse($rincian->tanggal_sejak)->translatedFormat('d F Y') }}</td>
                    <td class="px-6 py-4 text-red-500 font-medium whitespace-nowrap text-center align-middle">{{ Carbon\Carbon::parse($rincian->tanggal_akhir)->translatedFormat('d F Y') }}</td>
                    <td class="px-6 py-4 text-gray-600 truncate max-w-[150px] text-center align-middle" title="{{ $rincian->instansi_penerbit }}">{{ $rincian->instansi_penerbit }}</td>
                    <td class="px-6 py-4 text-gray-600 font-medium bg-red-50/50 text-center align-middle">{{ Carbon\Carbon::parse($rincian->tanggal_sejak)->translatedFormat('F') }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center align-middle">{{ $rincian->kegiatan }}</td>

                    <td class="px-6 py-4 align-middle">
                        <div class="flex gap-2 justify-center">
                            <button type="button" 
                                onclick="openModalEdit({
                                    id: '{{ $rincian->id }}',
                                    nama_perizinan: '{{ addslashes($rincian->nama_perizinan) }}',
                                    kegiatan: '{{ $rincian->kegiatan }}',
                                    nomor: '{{ addslashes($rincian->nomor) }}',
                                    tanggal_sejak: '{{ Carbon\Carbon::parse($rincian->tanggal_sejak)->format('Y-m') }}',
                                    tanggal_akhir: '{{ Carbon\Carbon::parse($rincian->tanggal_akhir)->format('Y-m') }}',
                                    instansi_penerbit: '{{ addslashes($rincian->instansi_penerbit) }}'
                                })" 
                                class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button type="button" onclick="openDeleteModal('modalHapusPerizinan', '/perizinan-perkantoran/{{ $rincian->id }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data rincian untuk periode ini.</td></tr>
            @endforelse
        </x-table>
        
        <div class="p-4 border-t border-gray-100 text-xs bg-white">
            {{ $dataRincian->links() }}
        </div>
    </x-card>

    <!-- ================= TABEL 3: PERIZINAN PROSES (SESUAI EXCEL MERGED) ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 mb-8 bg-white">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Perizinan Proses</h3>
                <p class="text-xs text-gray-400">Pemantauan progres perizinan yang masih berjalan</p>
            </div>
            <x-button variant="primary" onclick="openModalTambahProses()" class="shadow-sm text-xs border-none !py-2">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Perizinan Proses
            </x-button>
        </div>

        <x-table :headers="['Tahun', 'No.', 'Perizinan Proses', 'Target', 'Periode (Bulan)', 'Aksi']">
            @php $no = 1; @endphp
            
            @forelse($groupedProses as $group)
                @php $rowspan = $group->count(); @endphp
                
                @foreach($group as $index => $proses)
                    <tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-200">
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 text-gray-700 font-medium bg-gray-50/50 align-top border-r border-gray-200 text-center">{{ $proses->tahun }}</td>
                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 text-gray-700 font-medium bg-gray-50/50 align-top border-r border-gray-200 text-center">{{ $no++ }}</td>
                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 text-gray-900 font-bold align-top border-r border-gray-200 text-center">{{ $proses->nama_proses }}</td>
                        @endif
                        
                        <td class="px-6 py-4 text-gray-700 align-top text-center {{ $index > 0 ? 'border-t border-gray-100' : '' }}">{!! nl2br(e($proses->target)) !!}</td>
                        
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 text-gray-600 font-medium align-top border-l border-r border-gray-200 text-center">{{ $proses->periode }}</td>
                        @endif

                        <td class="px-6 py-4 align-top border-l border-gray-200 text-center {{ $index > 0 ? 'border-t border-gray-100' : '' }}">
                            <div class="flex gap-2 justify-center">
                                <button type="button" 
                                    onclick="openModalEditProses({
                                        id: '{{ $proses->id }}',
                                        tahun: '{{ $proses->tahun }}',
                                        nama_proses: '{{ addslashes($proses->nama_proses) }}',
                                        target: '{{ addslashes($proses->target) }}',
                                        periode: '{{ addslashes($proses->periode) }}'
                                    })" 
                                    class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Target">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusProses', '/perizinan-proses-list/{{ $proses->id }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Target">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data perizinan proses.</td></tr>
            @endforelse
        </x-table>
    </x-card>

    <x-delete-modal id="modalHapusPerizinan" title="Hapus Data Perizinan Terbit" message="Apakah Anda yakin ingin menghapus data perizinan terbit ini? Data yang dihapus tidak dapat dikembalikan." />
    <x-delete-modal id="modalHapusProses" title="Hapus Data Perizinan Proses" message="Apakah Anda yakin ingin menghapus progres perizinan ini? Data yang dihapus tidak dapat dikembalikan." />

    <!-- ================= MODAL TAMBAH / EDIT PERIZINAN TERBIT ================= -->
    <x-modal id="modalTambahRincian" title="Formulir Rincian Perizinan Terbit" description="Lengkapi detail dokumen perizinan perkantoran.">
        <form action="{{ route('perizinan-perkantoran.store') }}" method="POST" id="formPerizinan" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Perizinan Terbit <span class="text-red-500">*</span></label>
                <input type="text" name="nama_perizinan" id="form_nama_perizinan" required placeholder="Contoh: Pendaftaran NPP NPK" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bidang ini wajib diisi!</span>
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kegiatan <span class="text-red-500">*</span></label>
                <select name="kegiatan" id="form_kegiatan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 transition-colors">
                    <option value="" disabled selected>Pilih Kegiatan...</option>
                    <option value="Produk">Produk</option>
                    <option value="Aset">Aset</option>
                    <option value="Proyek">Proyek</option>
                    <option value="Peralatan Pabrik">Peralatan Pabrik</option>
                    <option value="Adm & Lainnya">Adm & Lainnya</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Kegiatan wajib dipilih!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Perizinan <span class="text-red-500">*</span></label>
                <input type="text" name="nomor" id="form_nomor" required placeholder="Contoh: PB-UMKU: 8120..." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Nomor wajib diisi!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Terbit (Bulan/Tahun) <span class="text-red-500">*</span></label>
                <input type="month" name="tanggal_sejak" id="form_tanggal_sejak" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bulan terbit wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Berakhir (Bulan/Tahun) <span class="text-red-500">*</span></label>
                <input type="month" name="tanggal_akhir" id="form_tanggal_akhir" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bulan berakhir wajib dipilih!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Instansi Penerbit <span class="text-red-500">*</span></label>
                <input type="text" name="instansi_penerbit" id="form_instansi_penerbit" required placeholder="Contoh: Kementerian Pertanian RI" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Instansi wajib diisi!</span>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-4 border-t border-gray-100 pt-5">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahRincian')" class="!rounded-xl !py-2.5 text-sm !px-6">Batal</x-button>
                <x-button variant="primary" type="submit" class="border-none shadow-md !py-2.5 text-sm !px-6">Simpan Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH / EDIT PERIZINAN PROSES ================= -->
    <x-modal id="modalTambahProses" title="Formulir Perizinan Proses" description="Masukkan atau perbarui tahapan perizinan yang sedang diproses.">
        <form action="{{ route('perizinan-proses.store') }}" method="POST" id="formProses" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodFieldProses" value="POST">
            
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                {{-- Input diubah ke type="text" agar flatpickr bekerja maksimal --}}
                <input type="text" name="tahun" id="form_proses_tahun" required placeholder="Pilih Tahun" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors cursor-pointer">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Tahun wajib diisi!</span>
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan)</label>
                <input type="text" name="periode" id="form_proses_periode" placeholder="Contoh: Desember 2025" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Perizinan Proses <span class="text-red-500">*</span></label>
                <input type="text" name="nama_proses" id="form_proses_nama" required placeholder="Contoh: Project Papua Barat" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Nama Proses wajib diisi!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Target</label>
                <textarea name="target" id="form_proses_target" rows="3" placeholder="Contoh: 1. AMDAL: Proses Pertek..." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors"></textarea>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-4 border-t border-gray-100 pt-5">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahProses')" class="!rounded-xl !py-2.5 text-sm !px-6">Batal</x-button>
                <x-button variant="primary" type="submit" class="border-none shadow-md !py-2.5 text-sm !px-6">Simpan Proses</x-button>
            </div>
        </form>
    </x-modal>

</main>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // ----------------------------------------------------
    // Inisialisasi Flatpickr Year Picker
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#form_proses_tahun", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true, // menampilkan Des bukan Desember
                    dateFormat: "Y", // format output di input field
                    altFormat: "Y",
                    theme: "light"
                })
            ],
            // Jika mode MonthSelect Plugin Year Only tidak sempurna, 
            // alternatifnya pakai konfigurasi dasar ini untuk membatasi input:
            // dateFormat: "Y",
            // static: true
        });
    });

    // ----------------------------------------------------
    // LOGIKA PERIZINAN TERBIT
    // ----------------------------------------------------
    const formPerizinan = document.getElementById('formPerizinan');
    const modalTitle = document.querySelector('#modalTambahRincian h3');
    const methodField = document.getElementById('methodField');

    function resetValidationTerbit() {
        const requiredFields = formPerizinan.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.classList.remove('border-red-500', 'bg-red-50');
            const errorSpan = field.nextElementSibling;
            if(errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
        });
    }

    function openModalTambah() {
        modalTitle.textContent = "Tambah Rincian Perizinan Terbit";
        formPerizinan.action = "{{ route('perizinan-perkantoran.store') }}";
        methodField.value = "POST";
        formPerizinan.reset();
        resetValidationTerbit();
        openModal('modalTambahRincian');
    }

    function openModalEdit(data) {
        modalTitle.textContent = "Edit Rincian Perizinan Terbit";
        formPerizinan.action = "/perizinan-perkantoran/" + data.id; 
        methodField.value = "PUT";
        
        document.getElementById('form_nama_perizinan').value = data.nama_perizinan;
        document.getElementById('form_kegiatan').value = data.kegiatan;
        document.getElementById('form_nomor').value = data.nomor;
        document.getElementById('form_tanggal_sejak').value = data.tanggal_sejak;
        document.getElementById('form_tanggal_akhir').value = data.tanggal_akhir;
        document.getElementById('form_instansi_penerbit').value = data.instansi_penerbit;

        resetValidationTerbit();
        openModal('modalTambahRincian');
    }

    // ----------------------------------------------------
    // LOGIKA PERIZINAN PROSES
    // ----------------------------------------------------
    const formProses = document.getElementById('formProses');
    const modalTitleProses = document.querySelector('#modalTambahProses h3');
    const methodFieldProses = document.getElementById('methodFieldProses');

    function resetValidationProses() {
        const requiredFields = formProses.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.classList.remove('border-red-500', 'bg-red-50');
            const errorSpan = field.nextElementSibling;
            if(errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
        });
    }

    function openModalTambahProses() {
        modalTitleProses.textContent = "Tambah Perizinan Proses";
        formProses.action = "{{ route('perizinan-proses.store') }}";
        methodFieldProses.value = "POST";
        formProses.reset();
        
        // Set tahun default ke tahun sekarang pada Flatpickr
        document.getElementById('form_proses_tahun')._flatpickr.setDate(new Date().getFullYear().toString());
        
        resetValidationProses();
        openModal('modalTambahProses');
    }

    function openModalEditProses(data) {
        modalTitleProses.textContent = "Edit Perizinan Proses";
        formProses.action = "/perizinan-proses-list/" + data.id; 
        methodFieldProses.value = "PUT";
        
        // Set tahun pada Flatpickr
        document.getElementById('form_proses_tahun')._flatpickr.setDate(data.tahun);
        
        document.getElementById('form_proses_nama').value = data.nama_proses;
        document.getElementById('form_proses_target').value = data.target;
        document.getElementById('form_proses_periode').value = data.periode;

        resetValidationProses();
        openModal('modalTambahProses');
    }

    // ----------------------------------------------------
    // VALIDASI MERAH CLIENT SIDE (KEDUA FORM)
    // ----------------------------------------------------
    [formPerizinan, formProses].forEach(form => {
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
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

            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                // Gunakan event 'change' juga untuk menangkap input dari flatpickr
                ['input', 'change'].forEach(evt => {
                    field.addEventListener(evt, function() {
                        const errorSpan = this.nextElementSibling;
                        if (this.value && this.value.trim() !== '') {
                            this.classList.remove('border-red-500', 'bg-red-50');
                            this.classList.add('border-gray-300');
                            if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                        }
                    });
                });
            });
        }
    });

    // ----------------------------------------------------
    // LOGIKA CHART.JS DINAMIS
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        const canvasPerizinan = document.getElementById('perizinanChart');
        if (canvasPerizinan) {
            const ctx = canvasPerizinan.getContext('2d');
            const rawChartData = {!! json_encode($chartData) !!};
            const labels = rawChartData.map(d => d.label);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Produk', data: rawChartData.map(d => d.produk), backgroundColor: '#A855F7', barPercentage: 0.5, categoryPercentage: 0.7 },
                        { label: 'Aset', data: rawChartData.map(d => d.aset), backgroundColor: '#2563EB', barPercentage: 0.5, categoryPercentage: 0.7 },
                        { label: 'Proyek', data: rawChartData.map(d => d.proyek), backgroundColor: '#EF4444', barPercentage: 0.5, categoryPercentage: 0.7 },
                        { label: 'Peralatan Pabrik', data: rawChartData.map(d => d.peralatan_pabrik), backgroundColor: '#22C55E', barPercentage: 0.5, categoryPercentage: 0.7 },
                        { label: 'Adm & Lainnya', data: rawChartData.map(d => d.adm), backgroundColor: '#F97316', barPercentage: 0.5, categoryPercentage: 0.7 }
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